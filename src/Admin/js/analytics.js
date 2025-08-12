// função para adicionar o link ativado no menu lateral ao link analytics
document.addEventListener("DOMContentLoaded", function() {
    document.querySelector("#link-analytics").innerHTML = `<li id="link-analytics">
            <a href="#" class="nav-link active" aria-current="page">
                <svg xmlns="http://www.w3.org/2000/svg" style="stroke:currentColor; stroke-width:1; color: #4F46E5;" width="16"
                    height="16" fill="currentColor" class="bi pe-none me-2 bi-graph-up" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                        d="M0 0h1v15h15v1H0zm14.817 3.113a.5.5 0 0 1 .07.704l-4.5 5.5a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61 4.15-5.073a.5.5 0 0 1 .704-.07" />
                </svg>
                Analytics
            </a>
        </li>`;

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

        async function getServerTime() {

            try {

                const result = await serverTime();

                if (result != null) {

                    const date = new Date(result.replace(' ', 'T'));

                    const formatted = date.toLocaleString('pt-PT', {
                        year: 'numeric',
                        month: 'numeric',
                        day: 'numeric',
                    });

                    return formatted;

                } else {

                    return "Error fetching the server time";

                }

            } catch (error) {

                return "Error: " + error.message;

            }
        }

    async function serverTime() {

        const response = await fetch('../../admin/getServerTimeStamp.php', {

            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            },
        });

        return await response.json();
    }

    const labels = ['January', 'February', 'March', 'April', 'May', 'June','July', 'August', 'September', 'October', 'November', 'December'];

    async function displayServerTime() {

        let serverTimeString = await getServerTime();

        let [day, month, year] = serverTimeString.split('/');

        day = Number(day.replace(/^0+/, ''));
        month = Number(month.replace(/^0+/, '')) - 1;
        year = Number(year);

        let thisYear = year;
        let lastYear = year - 1;

        let thisMonth = labels[month];
        let lastMonthIndex = (month - 1 + 12) % 12;
        let lastMonth = labels[lastMonthIndex];

        let thisDay = day;
        let lastDay = day - 1;

        const monthDays = {
            January: 31,
            February: (year % 4 === 0 && (year % 100 !== 0 || year % 400 === 0)) ? 29 : 28,
            March: 31,
            April: 30,
            May: 31,
            June: 30,
            July: 31,
            August: 31,
            September: 30,
            October: 31,
            November: 30,
            December: 31
        };

        if (lastDay === 0) {

            lastDay = monthDays[lastMonth];

        }

        const totalOrderfunctions = [() => getNumberOfOrdersMonth(thisMonth, lastMonth), () => getNumberOfOrdersYear(thisYear, lastYear), () => getNumberOfOrdersDay(thisDay, lastDay)];
        let currentOrderFunction = 0;

        $(document).on('click', '#total_orders_svg', function () {

            totalOrderfunctions[currentOrderFunction]();
            currentOrderFunction = (currentOrderFunction + 1) % totalOrderfunctions.length;

        });

        const revenuesfunctions = [() => getRevenueByMonth(thisMonth, lastMonth), () => getRevenueByYear(thisYear, lastYear), () => getRevenueByDay(thisDay, lastDay)];
        let currentRevenueFunction = 0;

        $(document).on('click', '#revenue_svg', function () {

            revenuesfunctions[currentRevenueFunction]();
            currentRevenueFunction = (currentRevenueFunction + 1) % revenuesfunctions.length;
            
        });

        const avgRevenuesfunctions = [() => getAvgRevenueByMonth(thisMonth, lastMonth), () => getAvgRevenueByYear(thisYear, lastYear), () => getAvgRevenueByDay(thisDay, lastDay)];
        let currentAvgRevenueFunction = 0;

        $(document).on('click', '#avg_revenue_svg', function () {

            avgRevenuesfunctions[currentAvgRevenueFunction]();
            currentAvgRevenueFunction = (currentAvgRevenueFunction + 1) % avgRevenuesfunctions.length;

        });

        const createdAccountsfunctions = [() => getNumberOfClientsByMonth(thisMonth, lastMonth), () => getNumberOfClientsByYear(thisYear, lastYear), () => getNumberOfClientsByDay(thisDay, lastDay)];
        let currentCreatedAccountsFunction = 0;

        $(document).on('click', '#created_accounts_svg', function () {

            createdAccountsfunctions[currentCreatedAccountsFunction]();
            currentCreatedAccountsFunction = (currentCreatedAccountsFunction + 1) % createdAccountsfunctions.length;

        });


        getClassificationFeedback();
        getEncomendasChart();
        getRevenuePerMonth();
        getTopProducts();
        getNumberOfOrdersDay(thisDay, lastDay);
        getRevenueByDay(thisDay, lastDay);
        getAvgRevenueByDay(thisDay, lastDay);
        getNumberOfClientsByDay(thisDay, lastDay);

    }
    
    displayServerTime();


    async function getClassificationFeedback() {

        const result = await classificationFeedback();

        if (result != null) {

            let reviews_value = Array.from({length: 5}, () => 0);
            let arraySum = 0;

            for (let i = 0; i < result.length; i++) {

                let index = result[i]["classificacao"] - 1;

                if (index >= 0 && index < reviews_value.length) {

                    reviews_value[index] = result[i]["Sum"];
                    arraySum += result[i]["Sum"];

                }
            }

            document.getElementById("one_star_perc").textContent  = ((reviews_value[0] / arraySum) * 100).toFixed(2) + "%";
            document.getElementById("two_star_perc").textContent  = ((reviews_value[1] / arraySum) * 100).toFixed(2) + "%";
            document.getElementById("three_star_perc").textContent = ((reviews_value[2] / arraySum) * 100).toFixed(2) + "%";
            document.getElementById("four_star_perc").textContent  = ((reviews_value[3] / arraySum) * 100).toFixed(2) + "%";
            document.getElementById("five_star_perc").textContent  = ((reviews_value[4] / arraySum) * 100).toFixed(2) + "%";


            const reviews_color = ['rgb(255, 0, 0)', 'rgb(255, 102, 0)', 'rgb(255, 204, 0)', 'rgb(102, 204, 0)', 'rgb(0, 153, 51)'];

            const data_reviews_chart = {
            labels: ['1 Star', '2 Star', '3 Star', '4 Star', '5 Star'],
            datasets: [
                {
                    label: 'Reviews',
                    data: reviews_value,
                    backgroundColor: reviews_color,
                }
            ]
            };

            const config_reviews_chart = {
            type: 'doughnut',
            data: data_reviews_chart,
            options: {
                responsive: true,
                plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Chart.js Doughnut Chart'
                }
                }
            },
            };

            new Chart(document.getElementById("reviews_chart"), config_reviews_chart);


        } else {
            
            const canvas = document.getElementById("reviews_chart");
            if (canvas) {

                canvas.remove();

            }

            const table = document.getElementById("reviews_table");

            if (table) {

                table.remove();

            }

            const parent = document.getElementById("reviews_div");
            if (parent) {

                const placeholder = document.createElement("div");
                placeholder.className = "placeholder-graph";
                placeholder.textContent = "No Reviews Found";
                const metricTitle = parent.querySelector(".metric-title");

                if (metricTitle && metricTitle.nextElementSibling) {

                    parent.insertBefore(placeholder, metricTitle.nextElementSibling.nextElementSibling);

                } else {

                    parent.appendChild(placeholder);

               }
            }
        }
    }

    async function classificationFeedback() {

        const response = await fetch('../../admin/getReviews.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            },
        });


        return await response.json();

    }

    async function getEncomendasChart() {

        const result = await encomendasChart();

        if (result != null) {

            const orderStatusSet = new Set();
            const ordersArray = {};
            const orderDatasets = [];

            const orderColors = {
                Pending:    { borderColor: 'rgb(255, 193, 7)', backgroundColor: 'rgba(255, 193, 7, 0.5)' },
                Received:   { borderColor: 'rgb(33, 150, 243)', backgroundColor: 'rgba(33, 150, 243, 0.5)' },
                Confirmed:  { borderColor: 'rgb(76, 175, 80)', backgroundColor: 'rgba(76, 175, 80, 0.5)' },
                Completed:  { borderColor: 'rgb(158, 158, 158)', backgroundColor: 'rgba(158, 158, 158, 0.5)' },
                Sent:       { borderColor: 'rgb(91, 192, 222)', backgroundColor: 'rgba(91, 192, 222, 0.5)' },
                Pendente:   { borderColor: 'rgb(255, 193, 7)', backgroundColor: 'rgba(255, 193, 7, 0.5)' },
                Recebido:   { borderColor: 'rgb(33, 150, 243)', backgroundColor: 'rgba(33, 150, 243, 0.5)' },
                Confirmado: { borderColor: 'rgb(76, 175, 80)', backgroundColor: 'rgba(76, 175, 80, 0.5)' },
                Completo:   { borderColor: 'rgb(158, 158, 158)', backgroundColor: 'rgba(158, 158, 158, 0.5)' },
                Enviado:    { borderColor: 'rgb(91, 192, 222)', backgroundColor: 'rgba(91, 192, 222, 0.5)' }
            };

            for (let i = 0; i < result.length; i++) {

                orderStatusSet.add(result[i]["status_encomenda"]);

            }

            orderStatusSet.forEach(arrayName => {

                ordersArray[arrayName] = Array.from({length: 12}, () => 0);

            })

            for (let i = 0; i < result.length; i++) {

                let month = result[i]["Month"].replace(/^0+/, '');

                ordersArray[result[i]["status_encomenda"]][month - 1] = result[i]["total"];

            }

            Array.from(orderStatusSet).forEach(status => {

                orderDatasets.push({

                    label: status,
                    data: ordersArray[status],
                    borderColor: orderColors[status]?.borderColor || 'rgb(111, 111, 111)',
                    backgroundColor: orderColors[status]?.backgroundColor || 'rgba(111, 111, 111, 0.5)'

                });
            });

            const data_order_statistics_chart = {
            labels: labels,
            datasets: orderDatasets
            };

            const config_order_statistics_chart = {
            type: 'line',
            data: data_order_statistics_chart,
            options: {
                responsive: true,
                plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Chart.js Line Chart'
                }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) {
                                if (Number.isInteger(value)) {
                                    return value;
                                }
                            },
                            stepSize: 1,
                            beginAtZero: true
                        }
                    }
                }
            }
            };

            new Chart(document.getElementById("order_statistics_chart"), config_order_statistics_chart);


        } else {
            
            const canvas = document.getElementById("order_statistics_chart");
            if (canvas) {

                canvas.remove();

            }

            const parent = document.getElementById("order_statistics_div");
            if (parent) {

                const placeholder = document.createElement("div");
                placeholder.className = "placeholder-graph";
                placeholder.textContent = "No Orders Found";
                const metricTitle = parent.querySelector(".metric-title");

                if (metricTitle && metricTitle.nextElementSibling) {

                    parent.insertBefore(placeholder, metricTitle.nextElementSibling.nextElementSibling);

                } else {

                    parent.appendChild(placeholder);

                }
            }
        }
    }

    async function getRevenuePerMonth() {

        const result = await revenuePerMonth();

        if (result != null) {

            let revenueData = Array.from({length: 12}, () => 0);

            for (let i = 0; i < result.length; i++) {

                let index = result[i]["Month"].replace('0', '');

                index = index - 1;

                if (index >= 0 && index < revenueData.length) {

                    revenueData[index] = result[i]["total"];

                }
            }

            const data_revenue_trends_chart = {
            labels: labels,
            datasets: [
                {
                    label: 'Revenue',
                    data: revenueData,
                    borderColor: 'rgb(33, 150, 243)',
                    backgroundColor: 'rgba(33, 150, 243, 0.5)',
                    borderWidth: 2,
                    borderRadius: 5,
                    borderSkipped: false,
                }
            ]
            };

            const config_revenue_trends_chart = {
            type: 'bar',
            data: data_revenue_trends_chart,
            options: {
                responsive: true,
                plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Chart.js Bar Chart'
                }
                }
            },
            };

            new Chart(document.getElementById("revenue_trends_chart"), config_revenue_trends_chart);


        } else {
            
            const canvas = document.getElementById("revenue_trends_chart");
            if (canvas) {

                canvas.remove();

            }

            const parent = document.getElementById("revenue_trends_div");
            if (parent) {

                const placeholder = document.createElement("div");
                placeholder.className = "placeholder-graph";
                placeholder.textContent = "No Revenues Found";
                const metricTitle = parent.querySelector(".metric-title");

                if (metricTitle && metricTitle.nextElementSibling) {

                    parent.insertBefore(placeholder, metricTitle.nextElementSibling.nextElementSibling);

                } else {

                    parent.appendChild(placeholder);

                }
            }
        }
    }

    async function getTopProducts() {

        const result = await topProducts();

        if (result != null) {

            const table = document.getElementById("top_products");

            if (table) {

                table.innerHTML = "";

                const thead = document.createElement("thead");
                thead.innerHTML = `
                    <tr>
                        <th scope="col">Product</th>
                        <th scope="col">Orders</th>
                        <th scope="col">Revenue</th>
                    </tr>
                `;
                table.appendChild(thead);

                const tbody = document.createElement("tbody");

                result.forEach(item => {

                    const tr = document.createElement("tr");

                    tr.innerHTML = `
                        <td scope="row">${item.nome_produto}</td>
                        <td>${item.total_encomendas}</td>
                        <td>${Number(item.total_dinheiro).toFixed(2)}€</td>
                    `;
                    tbody.appendChild(tr);

                });

                table.appendChild(tbody);

            }

        } else {

            const parent = document.getElementById("top_products_div");
            if (parent) {

                const placeholder = document.createElement("div");
                placeholder.className = "placeholder-graph";
                placeholder.textContent = "No Top Products Found";
                const metricTitle = parent.querySelector(".metric-title");

                if (metricTitle && metricTitle.nextElementSibling) {

                    parent.insertBefore(placeholder, metricTitle.nextElementSibling.nextElementSibling);

                } else {

                    parent.appendChild(placeholder);

                }
            }
        }
    }

    async function getNumberOfOrdersDay(thisDay, lastDay) {

        const resultThisDay = await numberOfOrdersDay(thisDay);
        const resultLastDay = await numberOfOrdersDay(lastDay);

        if (resultThisDay.status == 'success' && resultLastDay.status == 'success') {

            const currentOrders = resultThisDay.message[0]["NumberOfOrders"] ?? 0;
            const previousOrders = resultLastDay.message[0]["NumberOfOrders"] ?? 0;

            let percentage = 0;
            let percentageFormatted = 0;

            if (currentOrders != 0) { 

                percentage = ((currentOrders - previousOrders) / previousOrders) * 100;

                if (percentage != 0) {

                    percentageFormatted = percentage.toFixed(2);

                }
            }
            
            if (previousOrders != 0) { 

                percentage = ((currentOrders - previousOrders) / previousOrders) * 100;

                if (percentage != 0) {

                    percentageFormatted = percentage.toFixed(2);

                }
            }

            const totalOrders = document.getElementById("total_orders");

            if (totalOrders) {

                totalOrders.innerHTML = "";

                const isPositive = percentage > 0;

                const changeClass = percentage > 0 ? "metric-change positive" : percentage < 0 ? "metric-change negative" : "metric-change neutral";

                const arrowSvg = percentage > 0
                    ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg" class="me-1">
                        <path d="M12 4L12 20M12 4L5 11M12 4L19 11" stroke="#28a745" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                        </svg>`
                    : percentage < 0
                        ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="me-1">
                            <path d="M12 20L12 4M12 20L5 13M12 20L19 13" stroke="#dc3545" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>`
                        : `<svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="#b0b0b0">
                            <path d="M647-440H160v-80h487L423-744l57-56 320 320-320 320-57-56 224-224Z"/>
                        </svg>`;

                        totalOrders.innerHTML = `
                            <div class="metric-title-row">
                                <div id="total_orders" class="metric-title">Total Orders</div>
                                <span id="total_orders_svg" class="metric-title-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#acb1b6">
                                        <path d="M120-240v-80h240v80H120Zm0-200v-80h480v80H120Zm0-200v-80h720v80H120Z"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="metric-value">${currentOrders.toLocaleString()}</div>
                            <div class="${changeClass}">
                                ${arrowSvg}
                                ${isPositive ? "+" : ""}${percentageFormatted}%
                            </div>
                            <div class="comparison">vs. ${previousOrders.toLocaleString()} yesterday</div>
                        `;

            }

        } else {

            const totalOrders = document.getElementById("total_orders");

            if (totalOrders) {

                totalOrders.innerHTML = "";

                totalOrders.innerHTML = `
                    <div id="total_orders" class="metric-title">Total Orders</div>
                    <div class="metric-value negative">Error Connection to the API</div>
                `;
            }

        }
    }

    async function getNumberOfOrdersMonth(thisMonth, lastMonth) {


        const resultThisMonth = await numberOfOrdersMonth(thisMonth);
        const resultLastMonth = await numberOfOrdersMonth(lastMonth);

        if (resultThisMonth.status == 'success' && resultLastMonth.status == 'success') {

            const currentOrders = resultThisMonth.message[0]["NumberOfOrders"] ?? 0;
            const previousOrders = resultLastMonth.message[0]["NumberOfOrders"] ?? 0;

            let percentage = ((currentOrders - previousOrders) / previousOrders) * 100;
            let percentageFormatted = percentage.toFixed(2);

            const totalOrders = document.getElementById("total_orders");

            if (totalOrders) {

                totalOrders.innerHTML = "";

                const isPositive = percentage > 0;

                const changeClass = percentage > 0 ? "metric-change positive" : percentage < 0 ? "metric-change negative" : "metric-change neutral";

                const arrowSvg = percentage > 0
                    ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg" class="me-1">
                        <path d="M12 4L12 20M12 4L5 11M12 4L19 11" stroke="#28a745" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                        </svg>`
                    : percentage < 0
                        ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="me-1">
                            <path d="M12 20L12 4M12 20L5 13M12 20L19 13" stroke="#dc3545" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>`
                        : `<svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="#b0b0b0">
                            <path d="M647-440H160v-80h487L423-744l57-56 320 320-320 320-57-56 224-224Z"/>
                        </svg>`;

                        totalOrders.innerHTML = `
                            <div class="metric-title-row">
                                <div id="total_orders" class="metric-title">Total Orders</div>
                                <span id="total_orders_svg" class="metric-title-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#acb1b6">
                                        <path d="M120-240v-80h240v80H120Zm0-200v-80h480v80H120Zm0-200v-80h720v80H120Z"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="metric-value">${currentOrders.toLocaleString()}</div>
                            <div class="${changeClass}">
                                ${arrowSvg}
                                ${isPositive ? "+" : ""}${percentageFormatted}%
                            </div>
                            <div class="comparison">vs. ${previousOrders.toLocaleString()} last month</div>
                        `;

            }

        } else {

            const totalOrders = document.getElementById("total_orders");

            if (totalOrders) {

                totalOrders.innerHTML = "";

                totalOrders.innerHTML = `
                    <div id="total_orders" class="metric-title">Total Orders</div>
                    <div class="metric-value negative">Error Connection to the API</div>
                `;
            }

        }
    }

    async function getNumberOfOrdersYear(thisYear, lastYear) {

        const resultThisYear = await numberOfOrdersYear(thisYear);
        const resultLastYear = await numberOfOrdersYear(lastYear);

        if (resultThisYear.status == 'success' && resultLastYear.status == 'success') {

            const currentOrders = resultThisYear.message[0]["NumberOfOrders"] ?? 0;
            const previousOrders = resultLastYear.message[0]["NumberOfOrders"] ?? 0;

            let percentage = ((currentOrders - previousOrders) / previousOrders) * 100;
            let percentageFormatted = percentage.toFixed(2);

            const totalOrders = document.getElementById("total_orders");

            if (totalOrders) {

                totalOrders.innerHTML = "";

                const isPositive = percentage > 0;

                const changeClass = percentage > 0 ? "metric-change positive" : percentage < 0 ? "metric-change negative" : "metric-change neutral";

                const arrowSvg = percentage > 0
                    ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg" class="me-1">
                        <path d="M12 4L12 20M12 4L5 11M12 4L19 11" stroke="#28a745" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                        </svg>`
                    : percentage < 0
                        ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="me-1">
                            <path d="M12 20L12 4M12 20L5 13M12 20L19 13" stroke="#dc3545" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>`
                        : `<svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="#b0b0b0">
                            <path d="M647-440H160v-80h487L423-744l57-56 320 320-320 320-57-56 224-224Z"/>
                        </svg>`;

                        totalOrders.innerHTML = `
                            <div class="metric-title-row">
                                <div id="total_orders" class="metric-title">Total Orders</div>
                                <span id="total_orders_svg" class="metric-title-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#acb1b6">
                                        <path d="M120-240v-80h240v80H120Zm0-200v-80h480v80H120Zm0-200v-80h720v80H120Z"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="metric-value">${currentOrders.toLocaleString()}</div>
                            <div class="${changeClass}">
                                ${arrowSvg}
                                ${isPositive ? "+" : ""}${percentageFormatted}%
                            </div>
                            <div class="comparison">vs. ${previousOrders.toLocaleString()} last year</div>
                        `;

            }

        } else {

            const totalOrders = document.getElementById("total_orders");

            if (totalOrders) {

                totalOrders.innerHTML = "";

                totalOrders.innerHTML = `
                    <div id="total_orders" class="metric-title">Total Orders</div>
                    <div class="metric-value negative">Error Connection to the API</div>
                `;
            }

        }
    }

    async function getRevenueByDay(thisDay, lastDay) {

        const resultThisDay = await revenueByDay(thisDay);
        const resultLastDay = await revenueByDay(lastDay);

        if (resultThisDay.status == 'success' && resultLastDay.status == 'success') {

            const currentRevenue = resultThisDay.message[0]["Revenue"] ?? 0;
            const previousRevenue = resultLastDay.message[0]["Revenue"] ?? 0;

            let percentage = 0;
            let percentageFormatted = 0;

            if (currentRevenue != 0) { 

                percentage = ((currentRevenue - previousRevenue) / previousRevenue) * 100;

                if (percentage != 0) {

                    percentageFormatted = percentage.toFixed(2);

                }
            
            }

            if (previousRevenue != 0) { 

                percentage = ((currentRevenue - previousRevenue) / previousRevenue) * 100;

                if (percentage != 0) {

                    percentageFormatted = percentage.toFixed(2);

                }
            }

            const revenueMetricCard = document.getElementById("revenue_metric_card");

            if (revenueMetricCard) {

                revenueMetricCard.innerHTML = "";

                const isPositive = percentage > 0;

                const changeClass = percentage > 0 ? "metric-change positive" : percentage < 0 ? "metric-change negative" : "metric-change neutral";

                const arrowSvg = percentage > 0
                    ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg" class="me-1">
                        <path d="M12 4L12 20M12 4L5 11M12 4L19 11" stroke="#28a745" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                        </svg>`
                    : percentage < 0
                        ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="me-1">
                            <path d="M12 20L12 4M12 20L5 13M12 20L19 13" stroke="#dc3545" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>`
                        : `<svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="#b0b0b0">
                            <path d="M647-440H160v-80h487L423-744l57-56 320 320-320 320-57-56 224-224Z"/>
                        </svg>`;

                        revenueMetricCard.innerHTML = `
                            <div class="metric-title-row">
                                <div id="total_orders" class="metric-title">Revenue</div>
                                <span id="revenue_svg" class="metric-title-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#acb1b6">
                                        <path d="M120-240v-80h240v80H120Zm0-200v-80h480v80H120Zm0-200v-80h720v80H120Z"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="metric-value">${currentRevenue.toLocaleString()}€</div>
                            <div class="${changeClass}">
                                ${arrowSvg}
                                ${isPositive ? "+" : ""}${percentageFormatted}%
                            </div>
                            <div class="comparison">vs. ${previousRevenue.toLocaleString()}€ yesterday</div>
                        `;
            }

        } else {

            const revenueMetricCard = document.getElementById("revenue_metric_card");

            if (revenueMetricCard) {

                revenueMetricCard.innerHTML = "";

                revenueMetricCard.innerHTML = `
                    <div id="total_orders" class="metric-title">Revenue</div>
                    <div class="metric-value negative">Error Connection to the API</div>
                `;
            }

        }
    }

    async function getRevenueByMonth(thisMonth, lastMonth) {

        const resultThisMonth = await revenueByMonth(thisMonth);
        const resultLastMonth = await revenueByMonth(lastMonth);

        if (resultThisMonth.status == 'success' && resultLastMonth.status == 'success') {

            const currentRevenue = resultThisMonth.message[0]["Revenue"] ?? 0;
            const previousRevenue = resultLastMonth.message[0]["Revenue"] ?? 0;

            let percentage = 0;
            let percentageFormatted = 0;

            if (currentRevenue != 0) { 

                percentage = ((currentRevenue - previousRevenue) / previousRevenue) * 100;

                if (percentage != 0) {

                    percentageFormatted = percentage.toFixed(2);

                }
            
            }

            if (previousRevenue != 0) { 

                percentage = ((currentRevenue - previousRevenue) / previousRevenue) * 100;

                if (percentage != 0) {

                    percentageFormatted = percentage.toFixed(2);

                }
            }

            const revenueMetricCard = document.getElementById("revenue_metric_card");

            if (revenueMetricCard) {

                revenueMetricCard.innerHTML = "";

                const isPositive = percentage > 0;

                const changeClass = percentage > 0 ? "metric-change positive" : percentage < 0 ? "metric-change negative" : "metric-change neutral";

                const arrowSvg = percentage > 0
                    ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg" class="me-1">
                        <path d="M12 4L12 20M12 4L5 11M12 4L19 11" stroke="#28a745" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                        </svg>`
                    : percentage < 0
                        ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="me-1">
                            <path d="M12 20L12 4M12 20L5 13M12 20L19 13" stroke="#dc3545" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>`
                        : `<svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="#b0b0b0">
                            <path d="M647-440H160v-80h487L423-744l57-56 320 320-320 320-57-56 224-224Z"/>
                        </svg>`;

                        revenueMetricCard.innerHTML = `
                            <div class="metric-title-row">
                                <div id="total_orders" class="metric-title">Revenue</div>
                                <span id="revenue_svg" class="metric-title-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#acb1b6">
                                        <path d="M120-240v-80h240v80H120Zm0-200v-80h480v80H120Zm0-200v-80h720v80H120Z"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="metric-value">${currentRevenue.toLocaleString()}€</div>
                            <div class="${changeClass}">
                                ${arrowSvg}
                                ${isPositive ? "+" : ""}${percentageFormatted}%
                            </div>
                            <div class="comparison">vs. ${previousRevenue.toLocaleString()}€ last Month</div>
                        `;
            }

        } else {

            const revenueMetricCard = document.getElementById("revenue_metric_card");

            if (revenueMetricCard) {

                revenueMetricCard.innerHTML = "";

                revenueMetricCard.innerHTML = `
                    <div id="total_orders" class="metric-title">Revenue</div>
                    <div class="metric-value negative">Error Connection to the API</div>
                `;
            }

        }
    }

    async function getRevenueByYear(thisYear, lastYear) {

        const resultThisYear = await revenueByYear(thisYear);
        const resultLastYear = await revenueByYear(lastYear);

        if (resultThisYear.status == 'success' && resultLastYear.status == 'success') {

            const currentRevenue = resultThisYear.message[0]["Revenue"] ?? 0;
            const previousRevenue = resultLastYear.message[0]["Revenue"] ?? 0;

            let percentage = 0;
            let percentageFormatted = 0;

            if (currentRevenue != 0) { 

                percentage = ((currentRevenue - previousRevenue) / previousRevenue) * 100;

                if (percentage != 0) {

                    percentageFormatted = percentage.toFixed(2);

                }
            
            }

            if (previousRevenue != 0) { 

                percentage = ((currentRevenue - previousRevenue) / previousRevenue) * 100;

                if (percentage != 0) {

                    percentageFormatted = percentage.toFixed(2);

                }
            }

            const revenueMetricCard = document.getElementById("revenue_metric_card");

            if (revenueMetricCard) {

                revenueMetricCard.innerHTML = "";

                const isPositive = percentage > 0;

                const changeClass = percentage > 0 ? "metric-change positive" : percentage < 0 ? "metric-change negative" : "metric-change neutral";

                const arrowSvg = percentage > 0
                    ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg" class="me-1">
                        <path d="M12 4L12 20M12 4L5 11M12 4L19 11" stroke="#28a745" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                        </svg>`
                    : percentage < 0
                        ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="me-1">
                            <path d="M12 20L12 4M12 20L5 13M12 20L19 13" stroke="#dc3545" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>`
                        : `<svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="#b0b0b0">
                            <path d="M647-440H160v-80h487L423-744l57-56 320 320-320 320-57-56 224-224Z"/>
                        </svg>`;

                        revenueMetricCard.innerHTML = `
                            <div class="metric-title-row">
                                <div id="total_orders" class="metric-title">Revenue</div>
                                <span id="revenue_svg" class="metric-title-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#acb1b6">
                                        <path d="M120-240v-80h240v80H120Zm0-200v-80h480v80H120Zm0-200v-80h720v80H120Z"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="metric-value">${currentRevenue.toLocaleString()}€</div>
                            <div class="${changeClass}">
                                ${arrowSvg}
                                ${isPositive ? "+" : ""}${percentageFormatted}%
                            </div>
                            <div class="comparison">vs. ${previousRevenue.toLocaleString()}€ last Year</div>
                        `;
            }

        } else {

            const revenueMetricCard = document.getElementById("revenue_metric_card");

            if (revenueMetricCard) {

                revenueMetricCard.innerHTML = "";

                revenueMetricCard.innerHTML = `
                    <div id="total_orders" class="metric-title">Revenue</div>
                    <div class="metric-value negative">Error Connection to the API</div>
                `;
            }

        }
    }

    async function getAvgRevenueByDay(thisDay, lastDay) {

        const resultTotalRevenueThisDay = await revenueByDay(thisDay);
        const resultTotalRevenueLastDay = await revenueByDay(lastDay);

        const resultTotalOrdersThisDay = await numberOfOrdersDay(thisDay);
        const resultTotalOrdersLastDay = await numberOfOrdersDay(lastDay);

        if (resultTotalRevenueThisDay.status == 'success' && resultTotalRevenueLastDay.status == 'success' && resultTotalOrdersThisDay.status == 'success' && resultTotalOrdersLastDay.status == 'success') {

            const currentRevenue = resultTotalRevenueThisDay.message[0]["Revenue"] ?? 0;
            const previousRevenue = resultTotalRevenueLastDay.message[0]["Revenue"] ?? 0;

            const currentTotalOldersRevenue = resultTotalOrdersThisDay.message[0]["NumberOfOrders"] ?? 0;
            const previousTotalOldersRevenue = resultTotalOrdersLastDay.message[0]["NumberOfOrders"] ?? 0;

            let currentAvgRevenue = 0;
            let previousAvgRevenue = 0;
            let percentage = 0;
            let percentageFormatted = 0;

            if (currentTotalOldersRevenue != 0) { 

                currentAvgRevenue = currentRevenue / currentTotalOldersRevenue; 

                previousAvgRevenue = previousRevenue / previousTotalOldersRevenue;

                percentage = ((currentAvgRevenue - previousAvgRevenue) / previousAvgRevenue) * 100;

                if (percentage != 0) {

                    percentageFormatted = percentage.toFixed(2);

                }
            
            }

            if (previousTotalOldersRevenue != 0) { 

                previousAvgRevenue = previousRevenue / previousTotalOldersRevenue;

                percentage = ((currentAvgRevenue - previousAvgRevenue) / previousAvgRevenue) * 100;

                if (percentage != 0) {

                    percentageFormatted = percentage.toFixed(2);

                }
            }

            const avgRevenueMetricCard = document.getElementById("avg_revenue");

            if (avgRevenueMetricCard) {

                avgRevenueMetricCard.innerHTML = "";

                const isPositive = percentage > 0;

                const changeClass = percentage > 0 ? "metric-change positive" : percentage < 0 ? "metric-change negative" : "metric-change neutral";

                const arrowSvg = percentage > 0
                    ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg" class="me-1">
                        <path d="M12 4L12 20M12 4L5 11M12 4L19 11" stroke="#28a745" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                        </svg>`
                    : percentage < 0
                        ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="me-1">
                            <path d="M12 20L12 4M12 20L5 13M12 20L19 13" stroke="#dc3545" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>`
                        : `<svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="#b0b0b0">
                            <path d="M647-440H160v-80h487L423-744l57-56 320 320-320 320-57-56 224-224Z"/>
                        </svg>`;

                        avgRevenueMetricCard.innerHTML = `
                            <div class="metric-title-row">
                                <div id="total_orders" class="metric-title">Avg. Order Value</div>
                                <span id="avg_revenue_svg" class="metric-title-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#acb1b6">
                                        <path d="M120-240v-80h240v80H120Zm0-200v-80h480v80H120Zm0-200v-80h720v80H120Z"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="metric-value">${currentRevenue.toLocaleString()}€</div>
                            <div class="${changeClass}">
                                ${arrowSvg}
                                ${isPositive ? "+" : ""}${percentageFormatted}%
                            </div>
                            <div class="comparison">vs. ${previousRevenue.toLocaleString()}€ yesterday</div>
                        `;
            }

        } else {

            const avgRevenueMetricCard = document.getElementById("avg_revenue");

            if (avgRevenueMetricCard) {

                avgRevenueMetricCard.innerHTML = "";

                avgRevenueMetricCard.innerHTML = `
                    <div id="total_orders" class="metric-title">Avg. Order Value</div>
                    <div class="metric-value negative">Error Connection to the API</div>
                `;
            }

        }
    }

    async function getAvgRevenueByMonth(thisMonth, lastMonth) {


        const resultTotalRevenueThisMonth = await revenueByMonth(thisMonth);
        const resultTotalRevenueLastMonth = await revenueByMonth(lastMonth);

        const resultTotalOrdersThisMonth = await numberOfOrdersMonth(thisMonth);
        const resultTotalOrdersLastMonth = await numberOfOrdersMonth(lastMonth);

        if (resultTotalRevenueThisMonth.status == 'success' && resultTotalRevenueLastMonth.status == 'success' && resultTotalOrdersThisMonth.status == 'success' && resultTotalOrdersLastMonth.status == 'success') {

            const currentRevenue = resultTotalRevenueThisMonth.message[0]["Revenue"] ?? 0;
            const previousRevenue = resultTotalRevenueLastMonth.message[0]["Revenue"] ?? 0;

            const currentTotalOldersRevenue = resultTotalOrdersThisMonth.message[0]["NumberOfOrders"] ?? 0;
            const previousTotalOldersRevenue = resultTotalOrdersLastMonth.message[0]["NumberOfOrders"] ?? 0;

            let currentAvgRevenue = 0;
            let previousAvgRevenue = 0;
            let percentage = 0;
            let percentageFormatted = 0;

            if (currentTotalOldersRevenue != 0) { 

                currentAvgRevenue = currentRevenue / currentTotalOldersRevenue; 

                previousAvgRevenue = previousRevenue / previousTotalOldersRevenue;

                percentage = ((currentAvgRevenue - previousAvgRevenue) / previousAvgRevenue) * 100;

                if (percentage != 0) {

                    percentageFormatted = percentage.toFixed(2);

                }
            
            }

            if (previousTotalOldersRevenue != 0) { 

                previousAvgRevenue = previousRevenue / previousTotalOldersRevenue;

                percentage = ((currentAvgRevenue - previousAvgRevenue) / previousAvgRevenue) * 100;

                if (percentage != 0) {

                    percentageFormatted = percentage.toFixed(2);

                }
            }

            const avgRevenueMetricCard = document.getElementById("avg_revenue");

            if (avgRevenueMetricCard) {

                avgRevenueMetricCard.innerHTML = "";

                const isPositive = percentage > 0;

                const changeClass = percentage > 0 ? "metric-change positive" : percentage < 0 ? "metric-change negative" : "metric-change neutral";

                const arrowSvg = percentage > 0
                    ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg" class="me-1">
                        <path d="M12 4L12 20M12 4L5 11M12 4L19 11" stroke="#28a745" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                        </svg>`
                    : percentage < 0
                        ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="me-1">
                            <path d="M12 20L12 4M12 20L5 13M12 20L19 13" stroke="#dc3545" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>`
                        : `<svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="#b0b0b0">
                            <path d="M647-440H160v-80h487L423-744l57-56 320 320-320 320-57-56 224-224Z"/>
                        </svg>`;

                        avgRevenueMetricCard.innerHTML = `
                            <div class="metric-title-row">
                                <div id="total_orders" class="metric-title">Avg. Order Value</div>
                                <span id="avg_revenue_svg" class="metric-title-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#acb1b6">
                                        <path d="M120-240v-80h240v80H120Zm0-200v-80h480v80H120Zm0-200v-80h720v80H120Z"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="metric-value">${currentRevenue.toLocaleString()}€</div>
                            <div class="${changeClass}">
                                ${arrowSvg}
                                ${isPositive ? "+" : ""}${percentageFormatted}%
                            </div>
                            <div class="comparison">vs. ${previousRevenue.toLocaleString()}€ last Month</div>
                        `;
            }

        } else {

            const avgRevenueMetricCard = document.getElementById("avg_revenue");

            if (avgRevenueMetricCard) {

                avgRevenueMetricCard.innerHTML = "";

                avgRevenueMetricCard.innerHTML = `
                    <div id="total_orders" class="metric-title">Avg. Order Value</div>
                    <div class="metric-value negative">Error Connection to the API</div>
                `;
            }

        }
    }

    async function getAvgRevenueByYear(thisYear, lastYear) {

        const resultTotalRevenueThisYear = await revenueByYear(thisYear);
        const resultTotalRevenueLastYear = await revenueByYear(lastYear);

        const resultTotalOrdersThisYear = await numberOfOrdersDay(thisYear);
        const resultTotalOrdersLastYear = await numberOfOrdersDay(lastYear);

        if (resultTotalRevenueThisYear.status == 'success' && resultTotalRevenueLastYear.status == 'success' && resultTotalOrdersThisYear.status == 'success' && resultTotalOrdersLastYear.status == 'success') {

            const currentRevenue = resultTotalRevenueThisYear.message[0]["Revenue"] ?? 0;
            const previousRevenue = resultTotalRevenueLastYear.message[0]["Revenue"] ?? 0;

            const currentTotalOldersRevenue = resultTotalOrdersThisYear.message[0]["NumberOfOrders"] ?? 0;
            const previousTotalOldersRevenue = resultTotalOrdersLastYear.message[0]["NumberOfOrders"] ?? 0;

            let currentAvgRevenue = 0;
            let previousAvgRevenue = 0;
            let percentage = 0;
            let percentageFormatted = 0;

            if (currentTotalOldersRevenue != 0) { 

                currentAvgRevenue = currentRevenue / currentTotalOldersRevenue; 

                previousAvgRevenue = previousRevenue / previousTotalOldersRevenue;

                percentage = ((currentAvgRevenue - previousAvgRevenue) / previousAvgRevenue) * 100;

                if (percentage != 0) {

                    percentageFormatted = percentage.toFixed(2);

                }
            
            }

            if (previousTotalOldersRevenue != 0) { 

                previousAvgRevenue = previousRevenue / previousTotalOldersRevenue;

                percentage = ((currentAvgRevenue - previousAvgRevenue) / previousAvgRevenue) * 100;

                if (percentage != 0) {

                    percentageFormatted = percentage.toFixed(2);

                }
            }

            const avgRevenueMetricCard = document.getElementById("avg_revenue");

            if (avgRevenueMetricCard) {

                avgRevenueMetricCard.innerHTML = "";

                const isPositive = percentage > 0;

                const changeClass = percentage > 0 ? "metric-change positive" : percentage < 0 ? "metric-change negative" : "metric-change neutral";

                const arrowSvg = percentage > 0
                    ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg" class="me-1">
                        <path d="M12 4L12 20M12 4L5 11M12 4L19 11" stroke="#28a745" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                        </svg>`
                    : percentage < 0
                        ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="me-1">
                            <path d="M12 20L12 4M12 20L5 13M12 20L19 13" stroke="#dc3545" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>`
                        : `<svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="#b0b0b0">
                            <path d="M647-440H160v-80h487L423-744l57-56 320 320-320 320-57-56 224-224Z"/>
                        </svg>`;

                        avgRevenueMetricCard.innerHTML = `
                            <div class="metric-title-row">
                                <div id="total_orders" class="metric-title">Avg. Order Value</div>
                                <span id="avg_revenue_svg" class="metric-title-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#acb1b6">
                                        <path d="M120-240v-80h240v80H120Zm0-200v-80h480v80H120Zm0-200v-80h720v80H120Z"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="metric-value">${currentRevenue.toLocaleString()}€</div>
                            <div class="${changeClass}">
                                ${arrowSvg}
                                ${isPositive ? "+" : ""}${percentageFormatted}%
                            </div>
                            <div class="comparison">vs. ${previousRevenue.toLocaleString()}€ last Year</div>
                        `;
            }

        } else {

            const avgRevenueMetricCard = document.getElementById("avg_revenue");

            if (avgRevenueMetricCard) {

                avgRevenueMetricCard.innerHTML = "";

                avgRevenueMetricCard.innerHTML = `
                    <div id="total_orders" class="metric-title">Avg. Order Value</div>
                    <div class="metric-value negative">Error Connection to the API</div>
                `;
            }

        }
    }

    async function getNumberOfClientsByDay(thisDay, lastDay) {

        const resultThisDay = await clientsByDay(thisDay);
        const resultLastDay = await clientsByDay(lastDay);

        if (resultThisDay.status == 'success' && resultLastDay.status == 'success') {

            const currentRevenue = resultThisDay.message[0]["NumberOfClients"] ?? 0;
            const previousRevenue = resultLastDay.message[0]["NumberOfClients"] ?? 0;

            let percentage = 0;
            let percentageFormatted = 0;

            if (currentRevenue != 0) { 

                percentage = ((currentRevenue - previousRevenue) / previousRevenue) * 100;

                if (percentage != 0) {

                    percentageFormatted = percentage.toFixed(2);

                }
            
            }

            if (previousRevenue != 0) { 

                percentage = ((currentRevenue - previousRevenue) / previousRevenue) * 100;

                if (percentage != 0) {

                    percentageFormatted = percentage.toFixed(2);

                }
            }

            const createdAccountsMetricCard = document.getElementById("created_accounts");

            if (createdAccountsMetricCard) {

                createdAccountsMetricCard.innerHTML = "";

                const isPositive = percentage > 0;

                const changeClass = percentage > 0 ? "metric-change positive" : percentage < 0 ? "metric-change negative" : "metric-change neutral";

                const arrowSvg = percentage > 0
                    ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg" class="me-1">
                        <path d="M12 4L12 20M12 4L5 11M12 4L19 11" stroke="#28a745" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                        </svg>`
                    : percentage < 0
                        ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="me-1">
                            <path d="M12 20L12 4M12 20L5 13M12 20L19 13" stroke="#dc3545" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>`
                        : `<svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="#b0b0b0">
                            <path d="M647-440H160v-80h487L423-744l57-56 320 320-320 320-57-56 224-224Z"/>
                        </svg>`;

                        createdAccountsMetricCard.innerHTML = `
                            <div class="metric-title-row">
                                <div id="total_orders" class="metric-title">Created Accounts</div>
                                <span id="created_accounts_svg" class="metric-title-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#acb1b6">
                                        <path d="M120-240v-80h240v80H120Zm0-200v-80h480v80H120Zm0-200v-80h720v80H120Z"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="metric-value">${currentRevenue.toLocaleString()}</div>
                            <div class="${changeClass}">
                                ${arrowSvg}
                                ${isPositive ? "+" : ""}${percentageFormatted}%
                            </div>
                            <div class="comparison">vs. ${previousRevenue.toLocaleString()} yesterday</div>
                        `;
            }

        } else {

            const createdAccountsMetricCard = document.getElementById("created_accounts");

            if (createdAccountsMetricCard) {

                createdAccountsMetricCard.innerHTML = "";

                createdAccountsMetricCard.innerHTML = `
                    <div id="total_orders" class="metric-title">Created Accounts</div>
                    <div class="metric-value negative">Error Connection to the API</div>
                `;
            }

        }
    }

    async function getNumberOfClientsByMonth(thisMonth, lastMonth) {

        const resultThisMonth = await clientsByMonth(thisMonth);
        const resultLastMonth = await clientsByMonth(lastMonth);

        if (resultThisMonth.status == 'success' && resultLastMonth.status == 'success') {

            const currentRevenue = resultThisMonth.message[0]["NumberOfClients"] ?? 0;
            const previousRevenue = resultLastMonth.message[0]["NumberOfClients"] ?? 0;

            let percentage = 0;
            let percentageFormatted = 0;

            if (currentRevenue != 0) { 

                percentage = ((currentRevenue - previousRevenue) / previousRevenue) * 100;

                if (percentage != 0) {

                    percentageFormatted = percentage.toFixed(2);

                }
            
            }

            if (previousRevenue != 0) { 

                percentage = ((currentRevenue - previousRevenue) / previousRevenue) * 100;

                if (percentage != 0) {

                    percentageFormatted = percentage.toFixed(2);

                }
            }

            const createdAccountsMetricCard = document.getElementById("created_accounts");

            if (createdAccountsMetricCard) {

                createdAccountsMetricCard.innerHTML = "";

                const isPositive = percentage > 0;

                const changeClass = percentage > 0 ? "metric-change positive" : percentage < 0 ? "metric-change negative" : "metric-change neutral";

                const arrowSvg = percentage > 0
                    ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg" class="me-1">
                        <path d="M12 4L12 20M12 4L5 11M12 4L19 11" stroke="#28a745" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                        </svg>`
                    : percentage < 0
                        ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="me-1">
                            <path d="M12 20L12 4M12 20L5 13M12 20L19 13" stroke="#dc3545" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>`
                        : `<svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="#b0b0b0">
                            <path d="M647-440H160v-80h487L423-744l57-56 320 320-320 320-57-56 224-224Z"/>
                        </svg>`;

                        createdAccountsMetricCard.innerHTML = `
                            <div class="metric-title-row">
                                <div id="total_orders" class="metric-title">Created Accounts</div>
                                <span id="created_accounts_svg" class="metric-title-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#acb1b6">
                                        <path d="M120-240v-80h240v80H120Zm0-200v-80h480v80H120Zm0-200v-80h720v80H120Z"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="metric-value">${currentRevenue.toLocaleString()}</div>
                            <div class="${changeClass}">
                                ${arrowSvg}
                                ${isPositive ? "+" : ""}${percentageFormatted}%
                            </div>
                            <div class="comparison">vs. ${previousRevenue.toLocaleString()} last Month</div>
                        `;
            }

        } else {

            const createdAccountsMetricCard = document.getElementById("created_accounts");

            if (createdAccountsMetricCard) {

                createdAccountsMetricCard.innerHTML = "";

                createdAccountsMetricCard.innerHTML = `
                    <div id="total_orders" class="metric-title">Created Accounts</div>
                    <div class="metric-value negative">Error Connection to the API</div>
                `;
            }

        }
    }

    async function getNumberOfClientsByYear(thisYear, lastYear) {


        const resultThisYear = await clientsByYear(thisYear);
        const resultLastYear = await clientsByYear(lastYear);

        if (resultThisYear.status == 'success' && resultLastYear.status == 'success') {

            const currentRevenue = resultThisYear.message[0]["NumberOfClients"] ?? 0;
            const previousRevenue = resultLastYear.message[0]["NumberOfClients"] ?? 0;

            let percentage = 0;
            let percentageFormatted = 0;

            if (currentRevenue != 0) { 

                percentage = ((currentRevenue - previousRevenue) / previousRevenue) * 100;

                if (percentage != 0) {

                    percentageFormatted = percentage.toFixed(2);

                }
            
            }

            if (previousRevenue != 0) { 

                percentage = ((currentRevenue - previousRevenue) / previousRevenue) * 100;

                if (percentage != 0) {

                    percentageFormatted = percentage.toFixed(2);

                }
            }

            const createdAccountsMetricCard = document.getElementById("created_accounts");

            if (createdAccountsMetricCard) {

                createdAccountsMetricCard.innerHTML = "";

                const isPositive = percentage > 0;

                const changeClass = percentage > 0 ? "metric-change positive" : percentage < 0 ? "metric-change negative" : "metric-change neutral";

                const arrowSvg = percentage > 0
                    ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg" class="me-1">
                        <path d="M12 4L12 20M12 4L5 11M12 4L19 11" stroke="#28a745" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                        </svg>`
                    : percentage < 0
                        ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="me-1">
                            <path d="M12 20L12 4M12 20L5 13M12 20L19 13" stroke="#dc3545" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>`
                        : `<svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="#b0b0b0">
                            <path d="M647-440H160v-80h487L423-744l57-56 320 320-320 320-57-56 224-224Z"/>
                        </svg>`;

                        createdAccountsMetricCard.innerHTML = `
                            <div class="metric-title-row">
                                <div id="total_orders" class="metric-title">Created Accounts</div>
                                <span id="created_accounts_svg" class="metric-title-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#acb1b6">
                                        <path d="M120-240v-80h240v80H120Zm0-200v-80h480v80H120Zm0-200v-80h720v80H120Z"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="metric-value">${currentRevenue.toLocaleString()}</div>
                            <div class="${changeClass}">
                                ${arrowSvg}
                                ${isPositive ? "+" : ""}${percentageFormatted}%
                            </div>
                            <div class="comparison">vs. ${previousRevenue.toLocaleString()} last Year</div>
                        `;
            }

        } else {

            const createdAccountsMetricCard = document.getElementById("created_accounts");

            if (createdAccountsMetricCard) {

                createdAccountsMetricCard.innerHTML = "";

                createdAccountsMetricCard.innerHTML = `
                    <div id="total_orders" class="metric-title">Created Accounts</div>
                    <div class="metric-value negative">Error Connection to the API</div>
                `;
            }

        }
    }

    async function encomendasChart() {

        const response = await fetch('../../admin/getEncomendasChart.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            },
        });


        return await response.json();

    }

    async function classificationFeedback() {

        const response = await fetch('../../admin/getReviews.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            },
        });


        return await response.json();

    }

    async function revenuePerMonth() {

        const response = await fetch('../../admin/getRevenuePerMonth.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            },
        });


        return await response.json();

    }

    async function topProducts() {

        const response = await fetch('../../admin/getTopProducts.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            },
        });


        return await response.json();

    }

    async function numberOfOrdersDay(day) {

        const response = await fetch('../../admin/getNumberOfOrdersByDay.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({day: day})
        });


        return await response.json();

    }

    async function numberOfOrdersMonth(month) {

        const response = await fetch('../../admin/getNumberOfOrdersByMonth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({month: month})
        });


        return await response.json();

    }

    async function numberOfOrdersYear(year) {

        const response = await fetch('../../admin/getNumberOfOrdersByYear.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({year: year})
        });


        return await response.json();

    }

    async function revenueByDay(day) {

        const response = await fetch('../../admin/getRevenueByDay.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({day: day})
        });


        return await response.json();

    }

    async function revenueByMonth(month) {

        const response = await fetch('../../admin/getRevenueByMonth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({month: month})
        });


        return await response.json();

    }

    async function revenueByYear(year) {

        const response = await fetch('../../admin/getRevenueByYear.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({year: year})
        });


        return await response.json();

    }

    async function clientsByDay(day) {

        const response = await fetch('../../admin/getClientsByDay.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({day: day})
        });


        return await response.json();

    }

    async function clientsByMonth(month) {

        const response = await fetch('../../admin/getClientsByMonth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({month: month})
        });


        return await response.json();

    }

    async function clientsByYear(year) {

        const response = await fetch('../../admin/getClientsByYear.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({year: year})
        });


        return await response.json();

    }
});