// Smooth scrolling for anchor links
document.querySelectorAll('.sidebar-nav a').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();

        const targetId = this.getAttribute('href');
        const targetElement = document.querySelector(targetId);

        if (targetElement) {
            window.scrollTo({
                top: targetElement.offsetTop - 100,
                behavior: 'smooth'
            });

            // Update active class
            document.querySelectorAll('.sidebar-nav a').forEach(a => a.classList.remove('active'));
            this.classList.add('active');
        }
    });
});

// Update active nav item on scroll
window.addEventListener('scroll', function () {
    const sections = document.querySelectorAll('.section-content, [id]');
    let current = '';

    sections.forEach(section => {
        const sectionTop = section.offsetTop - 150;
        if (window.pageYOffset >= sectionTop) {
            current = section.getAttribute('id');
        }
    });

    if (current) {
        document.querySelectorAll('.sidebar-nav a').forEach(a => {
            a.classList.remove('active');
            if (a.getAttribute('href') === '#' + current) {
                a.classList.add('active');
            }
        });
    }
});

// Substituir a função de pesquisa atual (linhas que começam com "// Search functionality") pelo seguinte código:

// Search functionality for entire help center
document.getElementById('searchHelp').addEventListener('keyup', function () {
    const searchTerm = this.value.toLowerCase();

    // Reset all highlighting and visibility when search is empty
    if (searchTerm.length < 2) {
        // Remove all highlights
        document.querySelectorAll('.bg-warning').forEach(highlight => {
            const parent = highlight.parentNode;
            // Replace the highlighted span with its text content
            parent.replaceChild(document.createTextNode(highlight.textContent), highlight);
            // Normalize the parent to merge adjacent text nodes
            parent.normalize();
        });

        // Reset visibility of all elements
        document.querySelectorAll('.section-content, .subcategory-title, .accordion-item, p, ul, ol, table, h4').forEach(item => {
            item.style.display = '';
        });

        // Reset accordion items
        document.querySelectorAll('.accordion-collapse').forEach(collapse => {
            collapse.classList.remove('show');
        });
        document.querySelectorAll('.accordion-button').forEach(button => {
            button.classList.add('collapsed');
        });

        // Remove stored original content to prevent issues with repeated searches
        document.querySelectorAll('[data-original-content]').forEach(el => {
            delete el.dataset.originalContent;
        });

        // Remove no results message if it exists
        const noResultsMsg = document.getElementById('noResultsMessage');
        if (noResultsMsg) {
            noResultsMsg.remove();
        }

        return;
    }

    // Track if we found any matches
    let foundMatches = false;

    // Search in all content sections
    document.querySelectorAll('.section-content').forEach(section => {
        let sectionHasMatch = false;
        const sectionId = section.getAttribute('id');

        // Search in section titles
        const sectionTitle = section.querySelector('.category-title');
        if (sectionTitle && sectionTitle.textContent.toLowerCase().includes(searchTerm)) {
            sectionHasMatch = true;
        }

        // Search in subcategory titles and their content
        section.querySelectorAll('.subcategory-title').forEach(subTitle => {
            let subTitleHasMatch = subTitle.textContent.toLowerCase().includes(searchTerm);
            let contentHasMatch = false;

            // Get all siblings until next subcategory-title or end of parent
            let sibling = subTitle.nextElementSibling;
            while (sibling && !sibling.classList.contains('subcategory-title')) {
                // Check text content in paragraphs, lists, tables
                if (sibling.textContent.toLowerCase().includes(searchTerm)) {
                    contentHasMatch = true;
                    sibling.style.display = '';

                    // Highlight matching text in content
                    highlightMatches(sibling, searchTerm);
                } else {
                    // Only hide if it's not an accordion (FAQs are handled separately)
                    if (!sibling.classList.contains('accordion')) {
                        sibling.style.display = 'none';
                    }
                }
                sibling = sibling.nextElementSibling;
            }

            if (subTitleHasMatch || contentHasMatch) {
                subTitle.style.display = '';
                sectionHasMatch = true;

                // Highlight matching text in subtitle
                if (subTitleHasMatch) {
                    highlightMatches(subTitle, searchTerm);
                }
            } else {
                subTitle.style.display = 'none';
            }
        });

        // Search in accordion items (FAQ)
        section.querySelectorAll('.accordion-item').forEach(item => {
            const question = item.querySelector('.accordion-button').textContent.toLowerCase();
            const answer = item.querySelector('.accordion-body').textContent.toLowerCase();

            if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                item.style.display = '';
                sectionHasMatch = true;

                // Expand the accordion item if it matches
                const collapseElement = item.querySelector('.accordion-collapse');
                const buttonElement = item.querySelector('.accordion-button');

                if (collapseElement && buttonElement) {
                    buttonElement.classList.remove('collapsed');
                    collapseElement.classList.add('show');

                    // Highlight matching text
                    if (question.includes(searchTerm)) {
                        highlightMatches(buttonElement, searchTerm);
                    }
                    if (answer.includes(searchTerm)) {
                        highlightMatches(item.querySelector('.accordion-body'), searchTerm);
                    }
                }
            } else {
                item.style.display = 'none';
            }
        });

        // Show/hide entire section based on matches
        if (sectionHasMatch) {
            section.style.display = '';
            foundMatches = true;

            // Activate the corresponding sidebar link
            document.querySelectorAll('.sidebar-nav a').forEach(a => {
                if (a.getAttribute('href') === '#' + sectionId) {
                    a.classList.add('active');
                }
            });
        } else {
            section.style.display = 'none';

            // Deactivate the corresponding sidebar link
            document.querySelectorAll('.sidebar-nav a').forEach(a => {
                if (a.getAttribute('href') === '#' + sectionId) {
                    a.classList.remove('active');
                }
            });
        }
    });

    // Show a message if no matches found
    const noResultsMsg = document.getElementById('noResultsMessage');
    if (!foundMatches && searchTerm.length >= 2) {
        if (!noResultsMsg) {
            const msg = document.createElement('div');
            msg.id = 'noResultsMessage';
            msg.className = 'alert alert-info mt-4';
            msg.textContent = 'No results found for "' + searchTerm + '". Please try different keywords.';

            const searchContainer = document.querySelector('.search-help');
            searchContainer.parentNode.insertBefore(msg, searchContainer.nextSibling);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
    }
});

// Function to highlight matching text
function highlightMatches(element, searchTerm) {
    // Skip if element is null or doesn't have innerHTML
    if (!element || typeof element.innerHTML !== 'string') return;

    // Store original content to avoid re-highlighting
    if (!element.dataset.originalContent) {
        element.dataset.originalContent = element.innerHTML;
    }

    // Reset to original content
    element.innerHTML = element.dataset.originalContent;

    // Skip elements that contain other complex elements to avoid breaking the DOM
    if (element.querySelector('table, form, input, button, iframe')) {
        return;
    }

    // Create a case-insensitive regular expression for the search term
    const regex = new RegExp('(' + searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');

    // Replace matches with highlighted version
    element.innerHTML = element.innerHTML.replace(regex, '<span class="bg-warning">$1</span>');
}

// Melhorar a função de atualização do menu lateral durante a rolagem
window.addEventListener('scroll', function () {
    // Obter a posição atual de rolagem
    const scrollPosition = window.scrollY;

    // Selecionar todas as seções principais e subcategorias
    const sections = document.querySelectorAll('.section-content, [id]');

    // Variável para armazenar o ID da seção atual
    let currentSectionId = '';

    // Encontrar a seção atual com base na posição de rolagem
    sections.forEach(section => {
        const offset = 200;
        const sectionTop = section.offsetTop - offset;
        const sectionHeight = section.offsetHeight;

        if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
            const id = section.getAttribute('id');
            if (section.classList.contains('section-content')) {
                currentSectionId = id;
            } else {
                const parentSection = section.closest('.section-content');
                if (parentSection) {
                    currentSectionId = parentSection.getAttribute('id');
                }
            }
        }
    });

    // Atualizar a navegação lateral
    const sidebarNav = document.getElementById('sidebar-nav');
    if (currentSectionId && sidebarNav) {
        document.querySelectorAll('.sidebar-nav a.nav-link').forEach(a => {
            a.classList.remove('active');
        });

        const sectionLink = document.querySelector(`.sidebar-nav > .mb-3 > a.nav-link[href="#${currentSectionId}"]`);
        if (sectionLink) {
            sectionLink.classList.add('active');

            const sidebar = document.querySelector('.sidebar');
            if (sidebar) {
                const groupContainer = sectionLink.parentElement;

                if (groupContainer) {
                    const groupTop = groupContainer.offsetTop;
                    const groupHeight = groupContainer.offsetHeight;
                    const groupBottom = groupTop + groupHeight;

                    const sidebarScrollTop = sidebar.scrollTop;
                    const sidebarHeight = sidebar.clientHeight;
                    const margin = 10;

                    if (groupHeight <= sidebarHeight) {
                        if (groupTop < sidebarScrollTop) {
                            sidebar.scrollTo({
                                top: Math.max(0, groupTop - margin),
                                behavior: 'smooth'
                            });
                        } else if (groupBottom > (sidebarScrollTop + sidebarHeight)) {
                            sidebar.scrollTo({
                                top: groupBottom - sidebarHeight + margin,
                                behavior: 'smooth'
                            });
                        }
                    } else {
                        const sectionLinkTopInSidebar = sectionLink.offsetTop;
                        const sectionLinkBottomInSidebar = sectionLinkTopInSidebar + sectionLink.offsetHeight;

                        if (sectionLinkTopInSidebar < sidebarScrollTop + margin) {
                            sidebar.scrollTo({
                                top: Math.max(0, sectionLinkTopInSidebar - margin),
                                behavior: 'smooth'
                            });
                        } else if (sectionLinkBottomInSidebar > (sidebarScrollTop + sidebarHeight - margin)) {
                            sidebar.scrollTo({
                                top: sectionLinkBottomInSidebar - sidebarHeight + margin,
                                behavior: 'smooth'
                            });
                        }
                    }
                }
            }
        }
    }
});

// Prevent page scroll when scrolling sidebar at its limits
const sidebarElement = document.querySelector('.sidebar');
if (sidebarElement) {
    sidebarElement.addEventListener('wheel', function (event) {
        const { scrollTop, scrollHeight, clientHeight } = this;
        const delta = event.deltaY;
        const isAtTop = delta < 0 && scrollTop === 0;
        const isAtBottom = delta > 0 && scrollTop + clientHeight >= scrollHeight - 1; // -1 for potential subpixel issues

        if (isAtTop || isAtBottom) {
            event.preventDefault();
        }
    });
}

// Adicionar evento de limpeza para o campo de pesquisa
document.getElementById('searchHelp').addEventListener('search', function () {
    if (this.value === '') {
        // Simular um evento keyup para limpar os resultados
        this.dispatchEvent(new Event('keyup'));
    }
});

// Inicializar o menu lateral para destacar a seção atual no carregamento da página
document.addEventListener('DOMContentLoaded', function () {
    window.dispatchEvent(new Event('scroll'));
});

// Back to Top Button
const backToTopButton = document.getElementById('backToTop');

if (backToTopButton) {
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) { // Mostra o botão após 300px de scroll
            backToTopButton.classList.add('visible');
        } else {
            backToTopButton.classList.remove('visible');
        }
    });

    backToTopButton.addEventListener('click', (e) => {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}