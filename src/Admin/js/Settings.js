
document.addEventListener('DOMContentLoaded', function() {
    
    window.adminSettings = new AdminSettings();

    const adminID = document.getElementById("adminID");
    
    getAdminInfo(adminID);

    async function getAdminInfo(adminID) {

        const resultAdminInfo = await adminInfoByID(adminID.value);

        if (resultAdminInfo.status == 'success') {

            const adminName = resultAdminInfo.data["nome_admin"];
            const adminImage = resultAdminInfo.data["imagem_admin"];

            document.getElementById("admin_nome").textContent = adminName;
            document.getElementById("img-admin").src = adminImage;

        }
    }


    async function adminInfoByID(id_admin) {

        const response = await fetch('../../admin/getAdmin.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({id_admin: id_admin})
        });


        return await response.json();

    }


});

class AdminSettings {
    constructor() {
        this.adminId = null;
        this.apiBase = '../../restapi/PrintGoAPI.php'; 
        this.init();
    }

    init() {
        
        this.adminId = window.adminId || 1;
        console.log('Admin ID:', this.adminId);
        this.setupEventListeners();
        this.loadAdminData();
    }

    setupEventListeners() {
      
        document.querySelectorAll('.settings-tabs .nav-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                this.switchTab(e.target.dataset.tab);
            });
        });

       
        const generalForm = document.getElementById('general-form');
        if (generalForm) {
            generalForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.updateGeneralSettings();
            });
        }

        const passwordForm = document.getElementById('password-form');
        if (passwordForm) {
            passwordForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.updatePassword();
            });
        }

        const addMemberForm = document.getElementById('add-member-form');
        if (addMemberForm) {
            addMemberForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.addTeamMember();
            });
        }

        const createTeamForm = document.getElementById('create-team-form');
        if (createTeamForm) {
            createTeamForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.createTeam();
            });
        }
    }


switchTab(tabName) {
    console.log(`🔄 Switching to tab: ${tabName}`);
    

    document.querySelectorAll('.settings-tabs .nav-link').forEach(link => {
        link.classList.remove('active');
    });
    const activeTab = document.querySelector(`[data-tab="${tabName}"]`);
    if (activeTab) {
        activeTab.classList.add('active');
    }

    document.querySelectorAll('.tab-content').forEach(content => {
        content.style.display = 'none';
    });
    const activeContent = document.getElementById(`${tabName}-content`);
    if (activeContent) {
        activeContent.style.display = 'block';
    }

    console.log(`📊 Loading data for tab: ${tabName}`);
    if (tabName === 'teams') {
        this.loadTeamsData();
    } else if (tabName === 'members') {
        this.loadMembersData();
    } else if (tabName === 'general') {
        if (!document.getElementById('nome_admin').value) {
            this.loadAdminData();
        }
    }
}

    async makeApiCall(endpoint, options = {}) {
        const defaultOptions = {
            method: 'GET',
            headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        };

        const finalOptions = { ...defaultOptions, ...options };
        const url = `${this.apiBase}${endpoint}`;
        
        console.log(`Making API call to: ${url}`, finalOptions);

        try {
            const response = await fetch(url, finalOptions);
            
            console.log(`Response status: ${response.status}`);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error(`API Error Response:`, errorText);
                throw new Error(`HTTP ${response.status}: ${response.statusText}\nResponse: ${errorText}`);
            }

            const data = await response.json();
            console.log(`API Response:`, data);
            return data;
            
        } catch (error) {
            console.error(`API call failed for ${endpoint}:`, error);
            throw error;
        }
    }

    async loadAdminData() {
        this.showLoading('general', true);
        try {
            const data = await this.makeApiCall(`/adminInfoByID/${this.adminId}`);
            
            if (data && !data.error) {
                this.populateAdminForm(data);
            } else {
                this.showAlert('Error loading admin data: ' + (data.message || 'Unknown error'), 'danger');
            }
        } catch (error) {
            this.showAlert(`Error loading admin data: ${error.message}`, 'danger');
        } finally {
            this.showLoading('general', false);
        }
    }

    populateAdminForm(adminData) {
        const fields = {
            'nome_admin': adminData.nome_admin || '',
            'email_admin': adminData.email_admin || '',
            'contacto_admin': adminData.contacto_admin || '',
            'funcao_admin': adminData.funcao_admin || ''
        };

        Object.entries(fields).forEach(([fieldId, value]) => {
            const field = document.getElementById(fieldId);
            if (field) field.value = value;
        });
        
        const lastLoginField = document.getElementById('last-login');
        if (lastLoginField) {
            const creationDate = adminData.data_criacao_admin ? 
                new Date(adminData.data_criacao_admin).toLocaleDateString('pt-PT') : 
                'N/A';
            lastLoginField.textContent = `Account created: ${creationDate}`;
        }
    }

    async updateGeneralSettings() {
        const formData = new FormData(document.getElementById('general-form'));
        const data = Object.fromEntries(formData);
        data.id_admin = this.adminId;

        try {
            const result = await this.makeApiCall('/updateAdmin', {
                method: 'POST',
                body: JSON.stringify(data)
            });
            
            if (result.status === 'success') {
                this.showAlert('Information updated successfully', 'success');
            } else {
                this.showAlert(result.message || 'Error updating information', 'danger');
            }
        } catch (error) {
            this.showAlert(`Error updating settings: ${error.message}`, 'danger');
        }
    }

   
async updatePassword() {
    const formData = new FormData(document.getElementById('password-form'));
    const data = Object.fromEntries(formData);


    if (data.new_password !== data.confirm_password) {
        this.showAlert('Password confirmation does not match', 'danger');
        return;
    }

    if (data.new_password.length < 8) {
        this.showAlert('Password must be at least 8 characters long', 'danger');
        return;
    }

   
    const requestData = {
        id_admin: this.adminId,
        current_password: data.current_password.trim(),
        new_password: data.new_password.trim()
    };

    console.log('Sending password update request:', {
        ...requestData,
        current_password: '[HIDDEN]',
        new_password: '[HIDDEN]'
    });

    try {
        
        const result = await this.makeApiCall('/updateAdminPassword', {
            method: 'POST',
            body: JSON.stringify(requestData)
        });
        
        console.log('Password update response:', result);
        
        if (result.success) {
            this.showAlert('Password updated successfully', 'success');
            document.getElementById('password-form').reset();
        } else {
            this.showAlert(result.message || 'Error updating password', 'danger');
        }
    } catch (error) {
        console.error('Password update error:', error);
        this.showAlert(`Error updating password: ${error.message}`, 'danger');
    }
}

    async loadTeamsData() {
        this.showLoading('teams', true);
        try {
            const teamsData = await this.makeApiCall('/getTeams');

            if (Array.isArray(teamsData)) {
                this.displayTeams(teamsData);
                this.updateTeamsStats(teamsData);
            } else {
                this.displayTeams([]);
                this.updateTeamsStats([]);
                if (teamsData && teamsData.error) {
                    this.showAlert('Error loading teams: ' + teamsData.message, 'danger');
                }
            }
        } catch (error) {
            console.error('Error loading teams data:', error);
            this.showAlert('Error loading teams data', 'danger');
            this.displayTeams([]);
            this.updateTeamsStats([]);
        } finally {
            this.showLoading('teams', false);
        }
    }


async loadMembersData() {
    this.showLoading('members', true);
    try {
        
        let teamData = [];
        let usersData = [];
        let teamsForSelectData = [];

        try {
            const teamResponse = await this.makeApiCall('/teamMembers');
            if (Array.isArray(teamResponse)) {
                teamData = teamResponse;
            } else if (teamResponse && !teamResponse.error) {
                teamData = [];
            }
        } catch (e) {
            console.warn('teamMembers failed:', e);
            teamData = [];
        }

        try {
            const usersResponse = await this.makeApiCall('/getAllUsers');
            console.log('Users API response:', usersResponse); 
            if (Array.isArray(usersResponse)) {
                usersData = usersResponse;
            } else if (usersResponse && usersResponse.error) {
                console.error('Users API error:', usersResponse.message);
                this.showAlert('Error loading users: ' + usersResponse.message, 'danger');
            }
        } catch (e) {
            console.warn('getAllUsers failed:', e);
            this.showAlert('Failed to load users', 'danger');
            usersData = [];
        }

        try {
            const teamsResponse = await this.makeApiCall('/getTeamsForSelect');
            console.log('Teams API response:', teamsResponse); 
            if (Array.isArray(teamsResponse)) {
                teamsForSelectData = teamsResponse;
            } else if (teamsResponse && teamsResponse.error) {
                console.error('Teams API error:', teamsResponse.message);
                
                try {
                    const fallbackResponse = await this.makeApiCall('/getTeams');
                    if (Array.isArray(fallbackResponse)) {
                        teamsForSelectData = fallbackResponse;
                    }
                } catch (fallbackError) {
                    console.warn('Fallback teams request failed:', fallbackError);
                }
            }
        } catch (e) {
            console.warn('getTeamsForSelect failed:', e);
            teamsForSelectData = [];
        }

       
        this.displayTeamMembers(teamData);
        this.updateMemberStats(teamData);

        
        console.log('Populating user select with:', usersData.length, 'users');
        console.log('Populating team select with:', teamsForSelectData.length, 'teams');
        
        this.populateUserSelect(usersData);
        this.populateTeamSelect(teamsForSelectData);

    } catch (error) {
        console.error('Error loading members data:', error);
        this.showAlert(`Error loading members data: ${error.message}`, 'danger');
        this.displayTeamMembers([]);
        this.updateMemberStats([]);
        this.populateUserSelect([]);
        this.populateTeamSelect([]);
    } finally {
        this.showLoading('members', false);
    }
}

    displayTeams(teams) {
        const teamsList = document.getElementById('teamsList');
        if (!teamsList) return;

        if (!teams || teams.length === 0) {
            teamsList.innerHTML = '<p class="text-muted">No teams created yet.</p>';
            return;
        }

        teamsList.innerHTML = teams.map(team => `
            <div class="team-card card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-2">${team.nome_team || 'Unnamed Team'}</h6>
                            <p class="card-text text-muted small mb-2">${team.descricao_team || 'No description'}</p>
                            <div class="d-flex align-items-center text-muted small">
                                <i class="bi bi-people me-1"></i>
                                <span class="me-3">${team.member_count || 0} members</span>
                                <i class="bi bi-calendar me-1"></i>
                                <span>Created ${team.data_criacao_team ? new Date(team.data_criacao_team).toLocaleDateString('pt-PT') : 'Unknown'}</span>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="adminSettings.viewTeamMembers(${team.id_team})">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="adminSettings.deleteTeam(${team.id_team})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    displayTeamMembers(members) {
        const teamsList = document.getElementById('teamMembersList');
        if (!teamsList) return;

        if (!members || members.length === 0) {
            teamsList.innerHTML = '<p class="text-muted">No team members yet.</p>';
            return;
        }

        teamsList.innerHTML = members.map(member => `
            <div class="team-member-item d-flex justify-content-between align-items-center p-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="bg-${member.role === 'admin' ? 'success' : 'primary'} rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width: 40px; height: 40px; color: white; font-weight: bold;">
                        ${(member.first_name || member.nome_cliente || 'U').charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <p class="mb-0 fw-medium">${member.first_name || member.nome_cliente || 'Unknown'}</p>
                        <p class="text-muted small mb-0">${member.email || member.email_cliente || 'No email'}</p>
                        <p class="text-muted small mb-0">Team: ${member.team_name || 'Unknown'}</p>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-${member.role === 'admin' ? 'success' : 'light text-dark'} me-2">
                        ${member.role || 'member'}
                    </span>
                    <button class="btn btn-sm btn-outline-danger" onclick="adminSettings.removeMember(${member.id || member.id_cliente})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }

    populateUserSelect(users) {
        const select = document.getElementById('member_select');
        if (!select) {
            console.error('member_select element not found');
            return;
        }

        select.innerHTML = '<option value="">Select a user...</option>';
        
        if (users && Array.isArray(users) && users.length > 0) {
            users.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id_cliente;
                option.textContent = `${user.nome_cliente} (${user.email_cliente})`;
                select.appendChild(option);
            });
            console.log(`Added ${users.length} users to select`);
        } else {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'No users available';
            option.disabled = true;
            select.appendChild(option);
        }
    }

    populateTeamSelect(teams) {
        const select = document.getElementById('team_select');
        if (!select) {
            console.error('team_select element not found');
            return;
        }

        select.innerHTML = '<option value="">Select a team...</option>';
        
        if (teams && Array.isArray(teams) && teams.length > 0) {
            teams.forEach(team => {
                const option = document.createElement('option');
                option.value = team.id_team;
                option.textContent = team.nome_team;
                select.appendChild(option);
            });
            console.log(`Added ${teams.length} teams to select`);
        } else {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'No teams available';
            option.disabled = true;
            select.appendChild(option);
        }
    }

    updateTeamsStats(teams) {
        const stats = {
            'total-teams': teams ? teams.length : 0,
            'active-teams': teams ? teams.filter(t => t.status_team === 'active').length : 0,
            'total-team-members': teams ? teams.reduce((sum, team) => sum + parseInt(team.member_count || 0), 0) : 0
        };

        Object.entries(stats).forEach(([elementId, value]) => {
            const element = document.getElementById(elementId);
            if (element) element.textContent = value;
        });
    }

    updateMemberStats(members) {
        const stats = {
            'total-members': members ? members.length : 0,
            'active-members': members ? members.filter(m => m.status === 'active').length : 0,
            'admin-members': members ? members.filter(m => m.role === 'admin').length : 0
        };

        Object.entries(stats).forEach(([elementId, value]) => {
            const element = document.getElementById(elementId);
            if (element) element.textContent = value;
        });
    }

    async createTeam() {
        const formData = new FormData(document.getElementById('create-team-form'));
        const data = Object.fromEntries(formData);
        data.created_by_admin = this.adminId;

        try {
            const result = await this.makeApiCall('/createTeam', {
                method: 'POST',
                body: JSON.stringify(data)
            });
            
            if (result.success) {
                this.showAlert('Team created successfully', 'success');
                document.getElementById('create-team-form').reset();
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('createTeamModal'));
                if (modal) modal.hide();
                
                this.loadTeamsData();
            } else {
                this.showAlert(result.message || 'Error creating team', 'danger');
            }
        } catch (error) {
            this.showAlert('Error creating team', 'danger');
        }
    }

    async addTeamMember() {
        const formData = new FormData(document.getElementById('add-member-form'));
        const data = Object.fromEntries(formData);

        if (!data.id_cliente) {
            this.showAlert('Please select a user', 'danger');
            return;
        }

        if (!data.id_team) {
            this.showAlert('Please select a team', 'danger');
            return;
        }

        try {
            const result = await this.makeApiCall('/addTeamMember', {
                method: 'POST',
                body: JSON.stringify(data)
            });
            
            if (result.success) {
                this.showAlert('Team member added successfully', 'success');
                document.getElementById('add-member-form').reset();
                this.loadMembersData();
            } else {
                this.showAlert(result.message || 'Error adding team member', 'danger');
            }
        } catch (error) {
            this.showAlert('Error adding team member', 'danger');
        }
    }

    async removeMember(memberId) {
        if (!confirm('Are you sure you want to remove this member?')) {
            return;
        }

        try {
            const result = await this.makeApiCall(`/removeTeamMember/${memberId}`, {
                method: 'POST'
            });
            
            if (result.success) {
                this.showAlert('Team member removed successfully', 'success');
                this.loadMembersData();
            } else {
                this.showAlert(result.message || 'Error removing team member', 'danger');
            }
        } catch (error) {
            this.showAlert('Error removing team member', 'danger');
        }
    }

    async deleteTeam(teamId) {
        if (!confirm('Are you sure you want to delete this team? This action cannot be undone.')) {
            return;
        }

        try {
            const result = await this.makeApiCall('/deleteTeam', {
                method: 'POST',
                body: JSON.stringify({ team_id: teamId })
            });
            
            if (result.success) {
                this.showAlert('Team deleted successfully', 'success');
                this.loadTeamsData();
            } else {
                this.showAlert(result.message || 'Error deleting team', 'danger');
            }
        } catch (error) {
            this.showAlert('Error deleting team', 'danger');
        }
    }

    viewTeamMembers(teamId) {
        this.switchTab('members');
    }

    showAlert(message, type) {
        const alertContainer = document.getElementById('alert-container');
        if (!alertContainer) return;

        const alertClass = type === 'success' ? 'alert-success' : 'alert-success';

        alertContainer.innerHTML = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        if (type === 'success') {
            setTimeout(() => {
                const alert = alertContainer.querySelector('.alert');
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        }
    }

    showLoading(tabName, show) {
        const loadingElement = document.getElementById(`${tabName}-loading`);
        if (!loadingElement) return;

        if (show) {
            loadingElement.classList.add('show');
        } else {
            loadingElement.classList.remove('show');
        }
    }
}