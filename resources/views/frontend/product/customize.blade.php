@extends('frontend.layouts.app')
@section('title', 'Product Customization')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('frontend/v2/css/customization.css') }}">


    <div class="breadcrumb-section">
        <div class="breadcrumb-wrapper">
            <div class="container">
                <div class="row">
                    <div
                        class="col-12 d-flex justify-content-between justify-content-md-between  align-items-center flex-md-row flex-column">
                        <h3 class="breadcrumb-title"></h3>
                        <div class="breadcrumb-nav">
                            <nav aria-label="breadcrumb">
                                <ul>
                                    <li><a href="{{ route('frontend.homepage') }}">Home</a></li>
                                    <li aria-current="page">Product Customizer</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <main class="main">
        <div class="container">
            <div class="row">

                <div class="col-lg-8">
                    <div class="card-panel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Product Customiser</h5>
                            <div class="toolbar"></div>
                        </div>

                        <div id="panels">
                            <div class="mb-3">
                                <h6>1. Choose product</h6>
                                <div class="row g-2">
                                    <!-- product data now contains view-specific images -->
                                    <div class="col-12 col-md-12">
                                        <div class="option-card product-option active"
                                            data-product='@json($dataProduct)'>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    @if (!empty($dataProduct['img']['front']))
                                                        <img src="{{ $dataProduct['img']['front'] }}" 
                                                            alt="{{ $dataProduct['name'] }}"
                                                            class="img-fluid"
                                                            style="width: 100px; height: 120px; object-fit: cover; border-radius: 4px;">
                                                    @else
                                                        <img src="{{ $dataProduct['image'] }}" 
                                                            alt="{{ $dataProduct['name'] }}"
                                                            class="img-fluid"
                                                            style="width: 100px; height: 120px; object-fit: cover; border-radius: 4px;">
                                                    @endif

                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $dataProduct['name'] }}</div>
                                                    <div class="small-muted">From £{{ number_format($dataProduct['price'] ?? $dataProduct['price'] ?? 0, 2) }}</div>
                                                    <div class="small-muted">Qty: {{ $dataProduct['quantity'] }}</div>
                                                    <div class="small-muted">
                                                        Size:
                                                        @if(!empty($dataProduct['sizes']))
                                                            @foreach($dataProduct['sizes'] as $s)
                                                                    {{ $s['size_name'] }},
                                                                    <input type="hidden" name="stock_id[]" value="{{ $s['stock_id'] }}">
                                                            @endforeach
                                                        @else
                                                            N/A
                                                        @endif
                                                    </div>
                                                    <div class="small-muted">
                                                        Color: {{ $dataProduct['colorName'] }}
                                                        <input type="hidden" name="colorID" value="{{ $dataProduct['colorID'] }}">
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <h6>2. Print method</h6>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="option-card method-option active"
                                            data-method='{"id":"print","label":"Print","maxWidthCm":30,"setup":5.99}'>
                                            <div class="d-flex align-items-start">
                                                <div class="me-2"><i class="fa-solid fa-print fa-2x"
                                                        style="color:var(--red)"></i></div>
                                                <div>
                                                    <div class="fw-bold">Print</div>
                                                    <div class="small-muted">Max Width: 30cm · Setup £5.99</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="option-card method-option"
                                            data-method='{"id":"embroidery","label":"Embroidery","maxWidthCm":25,"setup":9.99}'>
                                            <div class="d-flex align-items-start">
                                                <div class="me-2"><i class="fa-solid fa-pen-nib fa-2x"
                                                        style="color:var(--red)"></i></div>
                                                <div>
                                                    <div class="fw-bold">Embroidery</div>
                                                    <div class="small-muted">Max Width: 25cm · Setup £9.99</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <h6>3. Position Guideline</h6>

                                <div class="accordion" id="positionAccordion">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#positionCollapse">
                                                Select Position
                                            </button>
                                        </h2>
                                        <div id="positionCollapse" class="accordion-collapse collapse show">
                                            <div class="accordion-body">
                                                <!-- Position buttons -->
                                                <div class="row g-2 mb-3">
                                                    @foreach ($guidelines as $guideline)
                                                        <div class="col-6 col-md-4 col-lg-3 mb-2">

                                                            <button class="btn btn-outline-dark w-100 pos-btn"
                                                                data-pos="{{ $guideline->position }}"
                                                                data-image="{{ asset('images/guidelines/' . $guideline->image) }}"
                                                                data-direction="{{ $guideline->direction }}">
                                                                {{ $guideline->position }} <!-- Position values are like : Left Sleeve, Right Sleeve, Top Chest, Center Chest, Right Chest, Left Chest, Top Back, Bottom Back, Shoulder Back, Center Back -->
                                                            </button>

                                                        </div>
                                                    @endforeach
                                                </div>

                                                

                                                <!-- Position image -->
                                                <div class="text-center">
                                                    <img id="positionImage" src="" class="img-fluid"
                                                        style="display: none;">
                                                    <div id="positionPlaceholder" class="text-muted">
                                                        Click on a position to see guidelines
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <h6>4. Upload / Add layers</h6>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label">Add image</label>
                                        <input class="form-control" id="addImagesInput" type="file" accept="image/*">
                                        <div class="small-muted mt-1">Supported: PNG, JPG. Max per file: 6 MB.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Add text</label>
                                        <input class="form-control" id="addTextInput"
                                            placeholder="Enter text (press Add)" />
                                        <div class="d-flex gap-2 mt-2 align-items-center">
                                            <select id="fontFamily" class="form-select form-select-sm"
                                                style="max-width:170px">
                                                <option value="Inter,system-ui">Inter</option>
                                                <option value="Montserrat,system-ui">Montserrat</option>
                                                <option value="Georgia,serif">Georgia</option>
                                                <option value="Impact,sans-serif">Impact</option>
                                                <option value="Courier New,monospace">Courier New</option>
                                                <option value="Verdana,sans-serif">Verdana</option>
                                            </select>

                                            <div class="input-group input-group-sm" style="max-width:140px">
                                                <input id="fontSizeInput" type="number" class="form-control"
                                                    value="18" min="8" max="200">
                                                <span class="input-group-text">px</span>
                                            </div>

                                            <div class="btn-group" role="group" aria-label="Font styles">
                                                <button id="boldToggle" class="btn btn-outline-secondary btn-sm"
                                                    title="Bold"><i class="fa-solid fa-bold"></i></button>
                                                <button id="italicToggle" class="btn btn-outline-secondary btn-sm"
                                                    title="Italic"><i class="fa-solid fa-italic"></i></button>
                                                <button id="underlineToggle" class="btn btn-outline-secondary btn-sm"
                                                    title="Underline"><i class="fa-solid fa-underline"></i></button>
                                            </div>
                                        </div>

                                        <div class="d-flex gap-2 align-items-center mt-2">
                                            <input id="textColorInput" type="color" value="#000000"
                                                title="Text color">
                                            <div class="d-flex gap-1 ms-2 d-none">
                                                <button class="btn btn-sm btn-outline-dark color-swatch"
                                                    data-color="#000000" style="background:#000;color:#fff"></button>
                                                <button class="btn btn-sm btn-outline-dark color-swatch"
                                                    data-color="#ffffff" style="background:#fff"></button>
                                                <button class="btn btn-sm btn-outline-dark color-swatch"
                                                    data-color="#dc2026" style="background:var(--red)"></button>
                                                <button class="btn btn-sm btn-outline-dark color-swatch"
                                                    data-color="#1b9dd9" style="background:#1b9dd9"></button>
                                            </div>
                                            <button id="addTextBtn" class="btn btn-red btn-sm ms-auto">Add Text</button>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <h6>5. Layers (current view)</h6>
                                <div id="layersList" class="list-group mb-2" style="max-height:220px; overflow:auto;">
                                </div>
                                <div class="d-flex gap-2">
                                    <button id="deleteLayerBtn" class="btn btn-outline-danger btn-sm ms-auto"
                                        title="Delete selected layer"><i class="bi bi-trash"></i> Delete</button>
                                </div>
                            </div>
                            <input type="hidden" id="customizationData" name="customization_data">

                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card-panel-dark position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong class="text-white">Live Preview</strong>
                                <div class="small-muted text-white-50">Choose view & layers</div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">

                            <div class="d-flex gap-2 align-items-center">
                                <div class="view-btns btn-group btn-group-sm me-2" role="group" aria-label="Views">
                                    <button class="btn btn-outline-light" data-view="front">Front</button>
                                    <button class="btn btn-outline-light" data-view="back">Back</button>
                                    <button class="btn btn-outline-light" data-view="left">Left</button>
                                    <button class="btn btn-outline-light" data-view="right">Right</button>
                                </div>

                            </div>
                        </div>


                        <div class="d-flex justify-content-between align-items-center mb-2">

                            <div class="d-flex gap-2 align-items-center">
                                <div class="zoom-controls">
                                    <button id="zoomOut" class="btn btn-sm btn-outline-light" title="Zoom out"><i
                                            class="bi bi-zoom-out"></i></button>
                                    <div class="input-group input-group-sm" style="width:72px;">
                                        <input id="zoomLevel" type="text" class="form-control text-center"
                                            value="100%" readonly>
                                    </div>
                                    <button id="zoomIn" class="btn btn-sm btn-outline-light" title="Zoom in"><i
                                            class="bi bi-zoom-in"></i></button>
                                </div>

                                <div class="ms-2">
                                    <button id="downloadPngBtn" class="btn btn-sm btn-light" title="Download PNG"><i
                                            class="bi bi-download"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="preview-shell" id="previewShell" tabindex="0" aria-label="Design preview area">
                            <div class="preview-canvas" id="previewCanvas" role="img" aria-label="Product preview">
                                <div class="loader-overlay" id="loaderOverlay">
                                    <div class="spinner-border text-light" role="status"></div>
                                    <div>Rendering preview...</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3" id="previewMeta">
                            <div class="small-muted">Product: <span id="currentProductName"
                                    class="fw-bold">T-Shirt</span></div>
                            <div class="small-muted">Method: <span id="currentMethod" class="fw-bold">Print</span></div>
                            <div class="small-muted">View: <span id="currentViewLabel" class="fw-bold">Front</span></div>
                        </div>

                        <hr class="mt-2 mb-2">

                        <div id="inspector" class="inspector" style="display:none">
                            <h6>Layer inspector</h6>
                            <div id="inspectorContent"></div>
                        </div>

                        <div class="d-flex gap-2 mt-2">
                            <div class="mt-2 text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="small-muted">Quantity: <span class="fw-bold">{{ $dataProduct['quantity'] ?? 1 }}</span></div>
                                </div>
                            </div>
                            <div class="mt-2 text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="small-muted">Total: <span id="totalPrice" class="fw-bold">£0.00</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-3">
                            <button id="saveContinueBtn" class="btn btn-success btn-sm">Save & Continue Shopping</button>
                            <button id="saveCheckoutBtn" class="btn btn-primary btn-sm">Save & Checkout</button>
                        </div>


                    </div>
                </div>

            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

    <script>
        const PRODUCT_QTY = {{ $dataProduct['quantity'] ?? 0 }};
        const PRODUCT_BASE_PRICE = {{ $dataProduct['price'] ?? 0 }};
        const PRINT_SETUP_COST = 5.99;
        const EMBROIDERY_SETUP_COST = 9.99;
    </script>

    <script>
        class ProductCustomiserSlimViews {
            constructor() {
                this.previewCanvas = document.getElementById('previewCanvas');
                this.layersList = document.getElementById('layersList');
                this.loader = document.getElementById('loaderOverlay');
                this.inspector = document.getElementById('inspector');
                this.inspectorContent = document.getElementById('inspectorContent');
                this.selectedLayerId = null;
                this.currentView = 'front';

                const firstProductEl = document.querySelector('.product-option[data-product]');
                const defaultProduct = firstProductEl ? JSON.parse(firstProductEl.getAttribute('data-product')) : {
                    id: 'tshirt',
                    name: 'T-Shirt',
                    img: {
                        front: '',
                        back: '',
                        left: '',
                        right: ''
                    },
                    baseWidthCm: 30
                };

                this.state = {
                    product: defaultProduct,
                    method: {
                        id: 'print',
                        label: 'Print',
                        maxWidthCm: 30,
                        setup: 5.99
                    },
                    position: 'centre-chest',
                    layers: [],
                    zoom: 1,
                    quantity: 1,
                };

                this.pricing = {
                    basePrice: PRODUCT_BASE_PRICE,
                    quantity: PRODUCT_QTY,
                    printSetup: PRINT_SETUP_COST,
                    embroiderySetup: EMBROIDERY_SETUP_COST
                };

                this.customizationData = [];

                this.updateHiddenField(); 

                // apply default view background
                this.applyViewBackground();

                this.initUI();
                this.render();
            }

            updateHiddenField() {
                this.cleanupArray();
                const dataString = JSON.stringify(this.customizationData);
                document.getElementById('customizationData').value = dataString;
                // console.log('Customization Data (Array):', this.customizationData);
            }

          recalcPrice() {
              const basePrice = this.pricing.basePrice;
              const quantity = this.pricing.quantity;
              
              // Count layers by method type
              let printLayersCount = 0;
              let embroideryLayersCount = 0;
              
              this.customizationData.forEach(layer => {
                  if (layer.method === 'print') {
                      printLayersCount++;
                  } else if (layer.method === 'embroidery') {
                      embroideryLayersCount++;
                  }
              });
              
              // Calculate total: 
              // (base price × quantity) + (print setup × print layers × quantity) + (embroidery setup × embroidery layers × quantity)
              const baseTotal = basePrice * quantity;
              const printTotal = this.pricing.printSetup * printLayersCount * quantity;
              const embroideryTotal = this.pricing.embroiderySetup * embroideryLayersCount * quantity;
              
              const total = baseTotal + printTotal + embroideryTotal;
              
              document.getElementById('totalPrice').textContent = '£' + total.toFixed(2);
              
            //   console.log('Price Calculation:', {
            //       basePrice,
            //       quantity,
            //       printLayersCount,
            //       embroideryLayersCount,
            //       baseTotal,
            //       printTotal,
            //       embroideryTotal,
            //       total
            //   });
          }

            cleanupArray() {
                Object.keys(this.customizationData).forEach(key => {
                    if (key !== 'length' && isNaN(parseInt(key))) {
                        delete this.customizationData[key];
                    }
                });
            }

            initUI() {
                this.bindProductOptions();
                this.bindMethodOptions();
                this.bindPositionButtons();
                this.bindAddImage();
                this.bindAddText();
                this.bindLayerControls();
                this.bindZoomControls();
                this.bindDownloadPng();
                this.bindQtyAndPricing();
                this.initTooltips();
                this.bindViewButtons();

                $('#boldToggle,#italicToggle,#underlineToggle').on('click', function() {
                    $(this).toggleClass('active');
                });
                $('#previewShell').on('click', function() {
                    $(this).focus();
                });
            }

            bindViewButtons() {
                const btns = document.querySelectorAll('.view-btns [data-view]');
                btns.forEach(b => {
                    b.addEventListener('click', () => {
                        btns.forEach(x => x.classList.remove('active'));
                        b.classList.add('active');
                        this.currentView = b.getAttribute('data-view');
                        document.getElementById('currentViewLabel').textContent = this.capitalize(this
                            .currentView);
                        this.applyViewBackground();
                        this.render();
                    });
                });
                // set default active
                const defaultBtn = document.querySelector('.view-btns [data-view="front"]');
                if (defaultBtn) defaultBtn.classList.add('active');
            }

            applyViewBackground() {
                const imgObj = this.state.product.img || {};
                const url = (imgObj[this.currentView] || '');
                if (url) this.previewCanvas.style.backgroundImage = `url('${url}')`;
                else this.previewCanvas.style.backgroundImage = "";
            }

            bindProductOptions() {
                document.querySelectorAll('.product-option').forEach(el => {
                    el.addEventListener('click', () => {
                        document.querySelectorAll('.product-option').forEach(x => x.classList.remove(
                            'active'));
                        el.classList.add('active');
                        const p = JSON.parse(el.getAttribute('data-product'));
                        this.state.product = p;
                        this.applyViewBackground();
                        document.getElementById('currentProductName').textContent = p.name;
                        this.render();
                        this.recalcPrice();
                    });
                });
            }

            bindMethodOptions() {
                document.querySelectorAll('.method-option').forEach(el => {
                    el.addEventListener('click', () => {
                        document.querySelectorAll('.method-option').forEach(x => x.classList.remove(
                            'active'));
                        el.classList.add('active');
                        const m = JSON.parse(el.getAttribute('data-method'));
                        this.state.method = m;
                        // this.customizationData.forEach(layer => {
                        //     layer.method = m.id;
                        // });

                        this.state.layers
                        .filter(layer => layer.view === this.currentView)
                        .forEach(layer => {
                            const dataIndex = this.customizationData.findIndex(l => l.layerId === layer.id);
                            if (dataIndex !== -1) {
                                this.customizationData[dataIndex].method = m.id;
                            }
                        });
                        document.getElementById('currentMethod').textContent = m.label;
                        this.recalcPrice();
                        this.updateHiddenField();
                    });
                });
            }

            bindPositionButtons() {
                const btns = document.querySelectorAll('.pos-btn');
                btns.forEach(b => {
                    b.addEventListener('click', () => {
                        btns.forEach(x => x.classList.remove('active','btn-dark'));
                        b.classList.add('active','btn-dark');
                        // prefer explicit pos-key if provided
                        const posKey = b.getAttribute('data-pos-key') || this.slugify(b.getAttribute('data-pos') || '');
                        const posLabel = b.getAttribute('data-pos') || posKey;
                        this.state.position = posKey;

                        // If the guideline provides exact coords, store them as a temporary 'lastGuidelineCoord'
                        const left = b.getAttribute('data-left');
                        const top = b.getAttribute('data-top');
                        const view = b.getAttribute('data-view') || b.getAttribute('data-direction') || null;

                        if (view) {
                            // set app view to match guideline view if provided
                            this.currentView = view.toLowerCase();
                            document.querySelectorAll('.view-btns [data-view]').forEach(btn => {
                                btn.classList.toggle('active', btn.getAttribute('data-view') === this.currentView);
                            });
                            document.getElementById('currentViewLabel').textContent = this.capitalize(this.currentView);
                            this.applyViewBackground();
                            this.render();
                        }

                        // store guideline-provided coords so addLayer can use them
                        if (left && top) {
                            this._lastGuideline = { leftPct: parseFloat(left), topPct: parseFloat(top) };
                        } else {
                            this._lastGuideline = null;
                        }

                        // show position image and placeholder update (this part already in jQuery section,
                        // but keep in case user clicks via non-jQuery event)
                        const imageUrl = b.getAttribute('data-image');
                        if (imageUrl) {
                            const imgEl = document.getElementById('positionImage');
                            if (imgEl) {
                                imgEl.src = imageUrl;
                                imgEl.style.display = '';
                            }
                            const placeholder = document.getElementById('positionPlaceholder');
                            if (placeholder) placeholder.style.display = 'none';
                            const accBtn = document.querySelector('.accordion-button');
                            if (accBtn) accBtn.textContent = 'Position: ' + posLabel;
                        }

                        this.updateHiddenField();
                    });
                });
            }


            bindAddImage() {
                const input = document.getElementById('addImagesInput');
                input.addEventListener('change', (e) => {
                    const files = Array.from(e.target.files || []);
                    files.forEach(file => {
                        if (!file.type.startsWith('image/')) return;
                        if (file.size > 6 * 1024 * 1024) {
                            alert('File too large (max 6MB)');
                            return;
                        }
                        const reader = new FileReader();
                        reader.onload = (ev) => {
                            const src = ev.target.result;
                            const approxWidth = Math.round(this.previewCanvas.clientWidth * 0.5);
                            this.addLayer({
                                type: 'image',
                                src,
                                pos: this.state.position,
                                widthPx: approxWidth,
                                heightPx: null,
                                rotate: 0,
                                opacity: 1,
                                borderRadiusPx: 0,
                                bgColor: 'transparent',
                                draggable: true,
                                editable: true,
                                view: this.currentView
                            });
                        };
                        reader.readAsDataURL(file);
                    });
                    input.value = '';
                });
            }

            bindAddText() {
                document.getElementById('addTextBtn').addEventListener('click', () => {
                    const text = document.getElementById('addTextInput').value.trim();
                    if (!text) {
                        alert('Enter text first');
                        return;
                    }
                    const fontFamily = document.getElementById('fontFamily').value;
                    const fontSize = Math.max(8, parseInt(document.getElementById('fontSizeInput').value ||
                    28));
                    const bold = document.getElementById('boldToggle').classList.contains('active');
                    const italic = document.getElementById('italicToggle').classList.contains('active');
                    const underline = document.getElementById('underlineToggle').classList.contains('active');
                    const color = document.getElementById('textColorInput').value || '#000';
                    this.addLayer({
                        type: 'text',
                        text,
                        pos: this.state.position,
                        fontFamily,
                        fontSize,
                        bold,
                        italic,
                        underline,
                        color,
                        opacity: 1,
                        widthPx: null,
                        bgColor: 'transparent',
                        rotate: 0,
                        draggable: true,
                        editable: true,
                        view: this.currentView
                    });
                    document.getElementById('addTextInput').value = '';
                });
            }

            bindLayerControls() {
                this.layersList.addEventListener('click', (e) => {
                    const li = e.target.closest('li');
                    if (!li) return;
                    this.selectLayer(li.getAttribute('data-id'));
                });

                document.getElementById('deleteLayerBtn').addEventListener('click', () => {
                    const id = this.selectedLayerId;
                    if (!id) return alert('Select a layer to delete');
                    if (!confirm('Delete selected layer?')) return;
                    this.removeLayer(id);
                });
            }

            bindZoomControls() {
                document.getElementById('zoomIn').addEventListener('click', () => this.setZoom(this.state.zoom + 0.1));
                document.getElementById('zoomOut').addEventListener('click', () => this.setZoom(Math.max(0.3, this.state
                    .zoom - 0.1)));
                document.getElementById('previewShell').addEventListener('wheel', (e) => {
                    if (e.ctrlKey) {
                        e.preventDefault();
                        const delta = e.deltaY > 0 ? -0.05 : 0.05;
                        this.setZoom(Math.min(2.5, Math.max(0.3, this.state.zoom + delta)));
                    }
                }, {
                    passive: false
                });
            }

            bindDownloadPng() {
                document.getElementById('downloadPngBtn').addEventListener('click', () => this.exportPNG());
            }

            bindQtyAndPricing() {
                this.recalcPrice();
            }

            initTooltips() {
                const tipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
                tipTriggerList.forEach((el) => new bootstrap.Tooltip(el, {
                    container: 'body'
                }));
            }

            addLayer(opts) {
                const id = 'ly_' + Math.random().toString(36).slice(2, 9);
                const layer = Object.assign({
                    id,
                    type: 'image',
                    pos: this.state.position,
                    widthPx: 120,
                    heightPx: null,
                    rotate: 0,
                    opacity: 1,
                    borderRadiusPx: 0,
                    bgColor: 'transparent',
                    zIndex: (this.state.layers.length ? Math.max(...this.state.layers.map(l => l.zIndex || 0)) : 0) + 1,
                    leftPct: null,   // will be set below
                    topPct: null,
                    draggable: true,
                    editable: true,
                    view: this.currentView
                }, opts);

                // If last guideline clicked provided absolute coords, use them
                if (this._lastGuideline && typeof this._lastGuideline.leftPct === 'number') {
                    layer.leftPct = this._lastGuideline.leftPct;
                    layer.topPct = this._lastGuideline.topPct;
                } else {
                    const p = this.positionToCss(layer.pos);
                    layer.leftPct = layer.leftPct != null ? layer.leftPct : p.left;
                    layer.topPct = layer.topPct != null ? layer.topPct : p.top;
                }

                // push into state layers
                this.state.layers.push(layer);

                // create customizationData object
                const layerObject = {
                    productId: this.state.product.id,
                    method: this.state.method.id,
                    position: layer.pos,
                    type: layer.type,
                    data: layer.type === 'text' ? {
                        text: layer.text,
                        fontFamily: layer.fontFamily,
                        fontSize: layer.fontSize,
                        color: layer.color,
                        bold: layer.bold,
                        italic: layer.italic,
                        underline: layer.underline
                    } : {
                        src: layer.src,
                        width: layer.widthPx,
                        height: layer.heightPx
                    },
                    zIndex: layer.zIndex,
                    layerId: layer.id,
                    leftPct: layer.leftPct,
                    topPct: layer.topPct,
                    view: layer.view
                };

                this.customizationData.push(layerObject);

                this.render();
                this.selectLayer(layer.id);
                this.updateHiddenField();
                this.recalcPrice();

                // make sure method is applied
                const newLayerIndex = this.customizationData.findIndex(l => l.layerId === layer.id);
                if (newLayerIndex !== -1) {
                    this.customizationData[newLayerIndex].method = this.state.method.id;
                }
            }


            removeLayer(id) {
                this.state.layers = this.state.layers.filter(l => l.id !== id);

                this.customizationData = this.customizationData.filter(l => l.layerId !== id);

                if (this.selectedLayerId === id) this.selectedLayerId = null;
                this.hideInspector();
                this.render();
                this.updateHiddenField();
                this.recalcPrice();
            }

            selectLayer(id) {
                this.selectedLayerId = id;
                Array.from(this.layersList.children).forEach(li => li.classList.toggle('active', li.getAttribute(
                    'data-id') === id));
                Array.from(this.previewCanvas.querySelectorAll('.layer-item')).forEach(n => n.classList.toggle(
                    'selected', n.dataset.id === id));
                this.showInspectorFor(id);
            }

            showInspectorFor(id) {
                const layer = this.state.layers.find(l => l.id === id);
                if (!layer) {
                    this.hideInspector();
                    return;
                }

                this.inspector.style.display = 'block';
                this.inspectorContent.innerHTML = ''; // build UI

                const header = document.createElement('div');
                header.innerHTML = `<div class="d-flex align-items-center justify-content-between mb-2">
                                        <div><strong>${layer.type === 'image' ? 'Image layer' : 'Text layer'}</strong><div class="small-muted">id: ${layer.id}</div></div>
                                        <div><small class="text-muted">view: ${this.capitalize(layer.view)}</small></div>
                                    </div>`;
                this.inspectorContent.appendChild(header);

                if (layer.type === 'image') {
                    const html = document.createElement('div');
                    html.innerHTML = `
                        <div class="mb-2">
                            <label class="form-label small-muted">Width (px)</label>
                            <input id="insWidth" type="number" class="form-control form-control-sm" value="${layer.widthPx || ''}" min="20">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small-muted">Height (px)</label>
                            <input id="insHeight" type="number" class="form-control form-control-sm" value="${layer.heightPx || ''}" min="10">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small-muted">Border radius (px)</label>
                            <input id="insRadius" type="number" class="form-control form-control-sm" value="${layer.borderRadiusPx || 0}" min="0">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small-muted">Opacity</label>
                            <input id="insOpacity" type="range" min="0" max="1" step="0.05" value="${layer.opacity}">
                            <div class="small-muted">Value: <span id="insOpacityVal">${layer.opacity}</span></div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small-muted">Rotate (degrees)</label>
                            <input id="insRotate" type="number" class="form-control form-control-sm" value="${layer.rotate || 0}" step="1">
                        </div>
                        <div class="d-flex gap-2">
                            <button id="insApply" class="btn btn-sm btn-primary">Apply</button>
                            <button id="insDelete" class="btn btn-sm btn-outline-danger">Delete</button>
                        </div>
                    `;
                    this.inspectorContent.appendChild(html);

                    // wire events
                    const opacityInput = html.querySelector('#insOpacity');
                    const opacityVal = html.querySelector('#insOpacityVal');
                    opacityInput.addEventListener('input', () => {
                        opacityVal.textContent = opacityInput.value;
                    });

                    html.querySelector('#insApply').addEventListener('click', () => {
                        layer.widthPx = parseInt(html.querySelector('#insWidth').value) || null;
                        layer.heightPx = parseInt(html.querySelector('#insHeight').value) || null;
                        layer.borderRadiusPx = parseInt(html.querySelector('#insRadius').value) || 0;
                        layer.opacity = parseFloat(html.querySelector('#insOpacity').value) || 1;
                        layer.rotate = parseFloat(html.querySelector('#insRotate').value) || 0;

                        // update customizationData
                        const cd = this.customizationData.find(l => l.layerId === layer.id);
                        if (cd && cd.type === 'image') {
                            cd.data.width = layer.widthPx;
                            cd.data.height = layer.heightPx;
                            cd.data.bgColor = undefined; // remove bgColor completely
                        }

                        this.render();
                        this.updateHiddenField();
                    });

                    html.querySelector('#insDelete').addEventListener('click', () => {
                        if (!confirm('Delete this layer?')) return;
                        this.removeLayer(layer.id);
                    });

                } else if (layer.type === 'text') {
                    const html = document.createElement('div');
                    html.innerHTML = `
                        <div class="mb-2">
                            <label class="form-label small-muted">Text</label>
                            <input id="insText" type="text" class="form-control form-control-sm" value="${this.escapeHtml(layer.text || '')}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small-muted">Font family</label>
                            <input id="insFont" type="text" class="form-control form-control-sm" value="${layer.fontFamily || 'Inter, system-ui'}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small-muted">Font size (px)</label>
                            <input id="insFontSize" type="number" class="form-control form-control-sm" value="${layer.fontSize || 28}" min="6">
                        </div>
                        <div class="mb-2 d-flex gap-2">
                            <div class="btn-group btn-group-sm" role="group">
                                <button id="insBold" class="btn btn-outline-secondary ${layer.bold ? 'active' : ''}">B</button>
                                <button id="insItalic" class="btn btn-outline-secondary ${layer.italic ? 'active' : ''}">I</button>
                                <button id="insUnderline" class="btn btn-outline-secondary ${layer.underline ? 'active' : ''}">U</button>
                            </div>
                            <input id="insTextColor" type="color" value="${layer.color || '#000000'}" class="form-control form-control-sm" style="max-width:60px">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small-muted">Opacity</label>
                            <input id="insTextOpacity" type="range" min="0" max="1" step="0.05" value="${layer.opacity}">
                            <div class="small-muted">Value: <span id="insTextOpacityVal">${layer.opacity}</span></div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small-muted">Rotate (degrees)</label>
                            <input id="insTextRotate" type="number" class="form-control form-control-sm" value="${layer.rotate || 0}" step="1">
                        </div>
                        <div class="d-flex gap-2">
                            <button id="insApplyText" class="btn btn-sm btn-primary">Apply</button>
                            <button id="insDeleteText" class="btn btn-sm btn-outline-danger">Delete</button>
                        </div>
                    `;
                    this.inspectorContent.appendChild(html);

                    const opacityInput = html.querySelector('#insTextOpacity');
                    const opacityVal = html.querySelector('#insTextOpacityVal');
                    opacityInput.addEventListener('input', () => {
                        opacityVal.textContent = opacityInput.value;
                    });

                    html.querySelector('#insApplyText').addEventListener('click', () => {
                        layer.text = html.querySelector('#insText').value;
                        layer.fontFamily = html.querySelector('#insFont').value || layer.fontFamily;
                        layer.fontSize = Math.max(6, parseInt(html.querySelector('#insFontSize').value) || layer.fontSize);
                        layer.bold = html.querySelector('#insBold').classList.contains('active');
                        layer.italic = html.querySelector('#insItalic').classList.contains('active');
                        layer.underline = html.querySelector('#insUnderline').classList.contains('active');
                        layer.color = html.querySelector('#insTextColor').value || '#000';
                        layer.opacity = parseFloat(html.querySelector('#insTextOpacity').value) || 1;
                        layer.rotate = parseFloat(html.querySelector('#insTextRotate').value) || 0;

                        const cd = this.customizationData.find(l => l.layerId === layer.id);
                        if (cd && cd.type === 'text') {
                            cd.data.text = layer.text;
                            cd.data.fontFamily = layer.fontFamily;
                            cd.data.fontSize = layer.fontSize;
                            cd.data.bold = layer.bold;
                            cd.data.italic = layer.italic;
                            cd.data.underline = layer.underline;
                            cd.data.color = layer.color;
                            cd.data.bgColor = undefined; // remove bgColor completely
                        }

                        this.render();
                        this.updateHiddenField();
                    });

                    html.querySelector('#insDeleteText').addEventListener('click', () => {
                        if (!confirm('Delete this layer?')) return;
                        this.removeLayer(layer.id);
                    });
                }
            }


            hideInspector() {
                this.inspector.style.display = 'none';
                this.inspectorContent.innerHTML = '';
                this.selectedLayerId = null;
                Array.from(this.previewCanvas.querySelectorAll('.layer-item.selected')).forEach(n => n.classList.remove(
                    'selected'));
            }

            setZoom(z) {
                this.state.zoom = Math.min(2.5, Math.max(0.3, z));
                this.previewCanvas.style.transform = `scale(${this.state.zoom})`;
                document.getElementById('zoomLevel').value = Math.round(this.state.zoom * 100) + '%';
            }

            exportPNG() {
                this.loader.style.display = 'flex';
                const originalTransform = this.previewCanvas.style.transform;
                this.previewCanvas.style.transform = 'scale(1)';
                Array.from(this.previewCanvas.querySelectorAll('.layer-item.selected')).forEach(el => el.classList
                    .remove('selected'));
                html2canvas(this.previewCanvas, {
                        scale: 2,
                        backgroundColor: null
                    }).then(canvas => {
                        const link = document.createElement('a');
                        link.download = 'design-preview.png';
                        link.href = canvas.toDataURL('image/png');
                        link.click();
                        link.remove();
                    }).catch((err) => {
                        console.error(err);
                        alert('Export failed');
                    })
                    .finally(() => {
                        this.previewCanvas.style.transform = originalTransform;
                        this.loader.style.display = 'none';
                    });
            }

            render() {
                // remove existing visual layer nodes
                Array.from(this.previewCanvas.querySelectorAll('.layer-item')).forEach(n => n.remove());
                // sort by zIndex ascending
                const layers = (this.state.layers || []).slice().filter(l => l.view === this.currentView).sort((a, b) =>
                    (a.zIndex || 0) - (b.zIndex || 0));
                layers.forEach(layer => {
                    const el = document.createElement('div');
                    el.className = 'layer-item';
                    el.dataset.id = layer.id;
                    // left/top in percent
                    const left = (layer.leftPct != null) ? layer.leftPct : this.positionToCss(layer.pos).left;
                    const top = (layer.topPct != null) ? layer.topPct : this.positionToCss(layer.pos).top;
                    el.style.left = left + '%';
                    el.style.top = top + '%';
                    el.style.transform = `translate(-50%,-50%) rotate(${layer.rotate || 0}deg)`;
                    el.style.opacity = (layer.opacity == null ? 1 : layer.opacity);
                    el.style.zIndex = layer.zIndex || 1;
                    if (layer.widthPx) el.style.width = layer.widthPx + 'px';
                    if (layer.heightPx) el.style.height = layer.heightPx + 'px';
                    if (layer.bgColor && layer.bgColor !== 'transparent') el.style.backgroundColor = layer
                        .bgColor;
                    if (layer.borderRadiusPx) el.style.borderRadius = (layer.borderRadiusPx || 0) + 'px';
                    if (layer.type === 'image') {
                        const img = document.createElement('img');
                        img.src = layer.src;
                        img.alt = 'Layer image';
                        img.style.width = '100%';
                        img.style.height = '100%';
                        img.style.objectFit = 'contain';
                        el.appendChild(img);
                    } else if (layer.type === 'text') {
                        const span = document.createElement('div');
                        span.className = 'layer-text';
                        span.textContent = layer.text;
                        span.style.fontFamily = layer.fontFamily || 'Inter, system-ui';
                        span.style.fontSize = (layer.fontSize || 28) + 'px';
                        span.style.color = layer.color || '#000';
                        span.style.fontWeight = layer.bold ? 700 : 500;
                        span.style.fontStyle = layer.italic ? 'italic' : 'normal';
                        span.style.textDecoration = layer.underline ? 'underline' : 'none';
                        if (layer.bgColor && layer.bgColor !== 'transparent') span.style.background = layer
                            .bgColor;
                        span.style.opacity = layer.opacity == null ? 1 : layer.opacity;
                        el.appendChild(span);
                    }
                    // add pointer events for drag (only if draggable)
                    el.addEventListener('mousedown', (ev) => this.startDrag(ev, layer.id));
                    el.addEventListener('touchstart', (ev) => this.startDrag(ev, layer.id), {
                        passive: true
                    });

                    this.previewCanvas.appendChild(el);
                });

                this.refreshLayerList();
                this.recalcPrice();
                this.setZoom(this.state.zoom);
            }

            slugify(s) {
                if (!s) return '';
                return String(s).trim().toLowerCase().replace(/\s+/g, '-').replace(/[^\w\-]+/g,'');
            }

            positionToCss(posKeyOrLabel) {
                // Accept either slug or human label
                const key = this.slugify(posKeyOrLabel);

                // default map as fallback (same values you had but keys ensure slug use)
                const map = {
                    'right-chest': { left: 35, top: 26 },
                    'centre-chest': { left: 50, top: 34 },
                    'center-chest': { left: 50, top: 34 }, // alias
                    'left-chest': { left: 65, top: 26 },
                    'top-chest': { left: 50, top: 30 },
                    'centre-back': { left: 50, top: 60 },
                    'top-back': { left: 50, top: 30 },
                    'shoulder-back': { left: 50, top: 45 },
                    'bottom-back': { left: 50, top: 78 },
                    'left-sleeve': { left: 50, top: 25 },
                    'right-sleeve': { left: 60, top: 25 }
                };
                const p = map[key] || map['centre-chest'];
                return { left: p.left, top: p.top };
            }

            refreshLayerList() {
                this.layersList.innerHTML = '';

                // gather all layers across views, sort by zIndex desc
                const layers = (this.state.layers || []).slice().sort((a, b) => (b.zIndex || 0) - (a.zIndex || 0));

                layers.forEach(l => {
                    const li = document.createElement('li');
                    li.className = 'list-group-item d-flex justify-content-between align-items-center';
                    li.setAttribute('data-id', l.id);

                    const viewLabel = (l.view || 'front');
                    const viewBadge = `<span class="badge bg-secondary ms-2 text-uppercase" style="font-size:10px">${viewLabel}</span>`;

                    const title = (l.type === 'image') ? `Image · ${l.pos}` :
                        `Text: "${(l.text || '').slice(0,30)}" · ${l.pos}`;

                    // show view badge and layer type
                    li.innerHTML = `<div style="min-width:0">
                                        <strong style="display:block;color:#000000; white-space:nowrap; overflow:hidden; text-overflow:ellipsis">${title}</strong>
                                        <small class="text-muted">${l.type} · z:${l.zIndex||0} ${viewBadge}</small>
                                    </div>
                                    <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-outline-secondary inspect-layer d-none" title="Inspect">Inspect</button>
                                    <button class="btn btn-outline-danger remove-layer" title="Remove">Del</button>
                                    </div>`;

                    this.layersList.appendChild(li);

                    // inspect button
                    li.querySelector('.inspect-layer').addEventListener('click', (e) => {
                        e.stopPropagation();
                        // ensure preview switches to the layer's view
                        if (l.view && l.view !== this.currentView) {
                            this.currentView = l.view;
                            // activate view button
                            document.querySelectorAll('.view-btns [data-view]').forEach(b => b.classList.toggle('active', b.getAttribute('data-view') === this.currentView));
                            document.getElementById('currentViewLabel').textContent = this.capitalize(this.currentView);
                            this.applyViewBackground();
                            this.render();
                        }
                        this.selectLayer(l.id);
                    });

                    // remove button
                    li.querySelector('.remove-layer').addEventListener('click', (e) => {
                        e.stopPropagation();
                        if (!l.editable) {
                            alert('Layer is locked for editing');
                            return;
                        }
                        if (!confirm('Delete this layer?')) return;
                        this.removeLayer(l.id);
                    });

                    // clicking the li selects and switches preview
                    li.addEventListener('click', () => {
                        if (l.view && l.view !== this.currentView) {
                            this.currentView = l.view;
                            document.querySelectorAll('.view-btns [data-view]').forEach(b => b.classList.toggle('active', b.getAttribute('data-view') === this.currentView));
                            document.getElementById('currentViewLabel').textContent = this.capitalize(this.currentView);
                            this.applyViewBackground();
                            this.render();
                        }
                        this.selectLayer(l.id);
                    });
                });
            }


            startDrag(ev, layerId) {
                ev.preventDefault();
                const layer = this.state.layers.find(l => l.id === layerId && l.view === this.currentView);
                if (!layer) return;
                if (!layer.draggable) {
                    // UX: a small flash or message
                    // eslint-disable-next-line no-console
                    console.log('Layer locked for dragging');
                    return;
                }
                const rect = this.previewCanvas.getBoundingClientRect();
                const pointer = this._getPointer(ev);
                this._drag.active = true;
                this._drag.id = layerId;
                this._drag.startX = pointer.x;
                this._drag.startY = pointer.y;
                this._drag.rect = rect;
                this._drag.initLeftPct = (layer.leftPct != null) ? layer.leftPct : (this.positionToCss(layer.pos).left);
                this._drag.initTopPct = (layer.topPct != null) ? layer.topPct : (this.positionToCss(layer.pos).top);
                this.selectLayer(layerId);
                document.body.style.userSelect = 'none';
            }

            onDragMove(ev) {
                if (!this._drag.active) return;
                ev.preventDefault();
                const pointer = this._getPointer(ev);
                const dx = pointer.x - this._drag.startX;
                const dy = pointer.y - this._drag.startY;
                const rect = this._drag.rect;
                if (!rect) return;
                const dxPct = (dx / rect.width) * 100;
                const dyPct = (dy / rect.height) * 100;
                const newLeft = Math.min(95, Math.max(5, this._drag.initLeftPct + dxPct));
                const newTop = Math.min(95, Math.max(5, this._drag.initTopPct + dyPct));
                const layer = this.state.layers.find(l => l.id === this._drag.id);
                if (!layer) return;
                layer.leftPct = newLeft;
                layer.topPct = newTop;
                const node = this.previewCanvas.querySelector(`.layer-item[data-id="${layer.id}"]`);
                if (node) {
                    node.style.left = newLeft + '%';
                    node.style.top = newTop + '%';
                }
            }

            onDragEnd() {
                if (!this._drag.active) return;
                const layer = this.state.layers.find(l => l.id === this._drag.id);
                if (layer) {
                    // persist to customizationData
                    const cd = this.customizationData.find(x => x.layerId === layer.id);
                    if (cd) {
                        cd.leftPct = layer.leftPct;
                        cd.topPct = layer.topPct;
                    }
                }
                this._drag.active = false;
                document.body.style.userSelect = '';
                this.updateHiddenField();
            }


            _getPointer(ev) {
                if (ev.touches && ev.touches[0]) return {
                    x: ev.touches[0].clientX,
                    y: ev.touches[0].clientY
                };
                return {
                    x: ev.clientX,
                    y: ev.clientY
                };
            }

            capitalize(s) {
                return s.charAt(0).toUpperCase() + s.slice(1);
            }
            escapeHtml(s) {
                return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            }
        }

        // initialize
        $(function() {
            const app = new ProductCustomiserSlimViews();
            window.customiserApp = app;
            document.getElementById('zoomLevel').value = Math.round(app.state.zoom * 100) + '%';

            $('.pos-btn').click(function() {
                // Remove active styles from all pos buttons and add to clicked
                $('.pos-btn').removeClass('active btn-dark').addClass('btn-outline-dark');
                $(this).addClass('active btn-dark').removeClass('btn-outline-dark');

                // Show image and placeholder hide
                const imageUrl = $(this).data('image');
                $('#positionImage').attr('src', imageUrl).show();
                $('#positionPlaceholder').hide();

                // Update accordion title
                const positionName = $(this).data('pos');
                $('.accordion-button').text('Position: ' + positionName);

                // If guideline carries a direction, switch view to that
                const direction = $(this).data('direction');
                if (direction) {
                    // normalize direction to lower-case and map to the view button names if needed
                    const view = String(direction).toLowerCase();
                    // set the app current view and update UI
                    if (window.customiserApp) {
                        window.customiserApp.currentView = view;
                        document.querySelectorAll('.view-btns [data-view]').forEach(b => b.classList.toggle('active', b.getAttribute('data-view') === view));
                        document.getElementById('currentViewLabel').textContent = window.customiserApp.capitalize(view);
                        window.customiserApp.applyViewBackground();
                        window.customiserApp.render();
                    }
                }
            });


            $('.pos-btn').first().click();


            // Helper: build sizes array for server. Adjust as you need per your size UI.
            function buildSizesPayload() {
                // If your product has sizes pick from UI; fallback to single size 0 with quantity state.pricing.quantity
                const qty = window.customiserApp ? (window.customiserApp.state.quantity || 1) : 1;
                return [{
                    size_id: 0,      // adapt if you have a size selector; else 0
                    quantity: qty,
                    ean: null
                }];
            }

            function sendCustomizationToSession(shippingAction) {


                // Collect size IDs from hidden inputs (Blade generated)
                const sizeIDs = Array.from(document.querySelectorAll('input[name="size_id[]"]'))
                    .map(el => el.value);

                // Get color ID
                const colorID = document.querySelector('input[name="colorID"]')?.value || null;

                const payload = {
                    product_id: window.customiserApp.state.product.id || '{{ $dataProduct["id"] ?? 0 }}',
                    product_name: window.customiserApp.state.product.name || '{{ $dataProduct["name"] ?? '' }}',
                    product_image: window.customiserApp.state.product.img ? (window.customiserApp.state.product.img.front || '') : '{{ $dataProduct["image"] ?? '' }}',
                    color_id: colorID,
                    size_ids: sizeIDs,
                    sizes: buildSizesPayload(),
                    customization_data: window.customiserApp.customizationData || [],
                    action: shippingAction // 'checkout' or 'continue'
                };

                // send via AJAX
                $.ajax({
                    url: '{{ route("customiser.add_to_session") }}', 
                    method: 'POST',
                    data: JSON.stringify(payload),
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success(resp) {

                        // console.log(resp);

                        if (resp && resp.success) {
                            if (shippingAction === 'checkout') {
                                window.location.href = '{{ route("checkout") }}';
                            } else {
                                window.location.href = '{{ route("frontend.homepage") }}'; 
                            }
                        } else {
                            alert('Failed to save. Please try again.');
                        }
                    },
                    error(xhr) {
                        console.error(xhr);
                        alert('An error occurred while saving the customization.');
                    }
                });
            }

            $('#saveContinueBtn').on('click', function() {
                sendCustomizationToSession('continue');
            });

            $('#saveCheckoutBtn').on('click', function() {
                sendCustomizationToSession('checkout');
            });






        });
    </script>
@endsection