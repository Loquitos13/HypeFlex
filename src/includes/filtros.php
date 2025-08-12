<div class="offcanvas offcanvas-end" tabindex="-1" id="filtros_mob" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasRightLabel">Filtros</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="container">
            <div class="row align-items-start">
                <div class="filtros-mobile w-100">
                    <div id="categorias-container-mobile">
                        <h6>Categories</h6>
                    </div>
                    <hr>
                    <div class="range-container">
                        <h6 class="mb-3 fw-semibold">Price Range</h6>
                        <div class="d-flex gap-2 align-items-center">
                            <label for="price-min-input-mobile" class="form-label mb-0">Min:</label>
                            <input type="number" class="form-control" id="price-min-input-mobile" value="0" min="0"
                                step="1">
                            <label for="price-max-input-mobile" class="form-label mb-0">Max:</label>
                            <input type="number" class="form-control" id="price-max-input-mobile" value="100" min="0"
                                step="1">
                        </div>
                    </div>
                    <hr>
                    <div class="color-selector" id="color-selector-mobile">
                        <h6>Colors</h6>
                        <div id="idColorOptions-mobile" class="colorOptions d-flex gap-2"></div>
                    </div>
                    <hr>
                    <div>
                        <h6>Size</h6>
                        <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups"
                            id="size-selector-mobile"></div>
                    </div>
                    <hr>
                    <!-- Botão de Aplicar -->
                    <button id="apply-filters" class="btn btn-primary">Apply Filters</button>
                    &nbsp;
                    <button type="button" id="clear-filters-mobile" class="btn btn-outline-danger">Clear Filters</button>
                </div>
            </div>
            <hr>
        </div>
    </div>
</div>