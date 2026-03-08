<div class="card shadow-lg mt-3">
    <div class="card-body">
        <h2 class="text-primary text-uppercase mb-3">Modals</h2>
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="border border-dashed border-primary rounded p-3 w-auto mb-3">
                    <h3 class="text-primary mb-3">Modal success</h3>
                    {{-- snippet container --}}
                    <div class="component-snippets-container">
                        <div class="component-snippets-preview">
                            <div id="liveAlertPlaceholder">
                                <div></div>
                            </div>
                            <div>
                                <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#success-modal">
                                    {{translate('Success modal')}}
                                </button>
                                {{-- modal --}}
                                <div class="modal fade" id="success-modal" tabindex="-1" aria-labelledby="toggle-modal" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content shadow-lg">
                                            <div class="modal-header border-0 pb-0 d-flex justify-content-end">
                                                <button type="button" class="btn-close border-0 btn-circle bg-section2 shadow-none"
                                                    data-bs-dismiss="modal" aria-label="Close">
                                                </button>
                                            </div>
                                            <div class="modal-body px-20 py-0 mb-30">
                                                <div class="d-flex flex-column align-items-center text-center mb-30">
                                                    <img src="{{  dynamicAsset(path: 'public/assets/new/back-end/img/icons/modal-success-logo.png') }}"
                                                        width="80" class="mb-20" alt="">
                                                    <h2 class="modal-title mb-3">System File Uploaded Successfully!</h2>
                                                    <div class="text-center">Your chosen theme are successfully uploaded. If want to active this theme
                                                        click Active, or you can ignore.</div>
                                                </div>
                                                <div class="d-flex justify-content-center gap-3">
                                                    <button type="button" class="btn btn-primary max-w-180 flex-grow-1"
                                                        data-bs-dismiss="modal">{{ translate('Okay, Got it') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="position-relative snippets-code-hover">
                            <div class="component-snippets-code-header">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#html-tab-pane"
                                            type="button" role="tab" aria-controls="html-tab-pane" aria-selected="true">Html</button>
                                </ul>
                                <button class="btn btn-icon copy-button">
                                    <i class="fi fi-rr-copy"></i>
                                </button>
                            </div>
                            <div class="tab-content content position-absolute start-0 top-100 shadow-lg bg-white p-1 z-3 mw-100"
                                id="myTabContent">
                                <div class="tab-pane fade show active" id="html-tab-pane" role="tabpanel" aria-labelledby="home-tab"
                                    tabindex="0">
                                    <div class="component-snippets-code-container">
<pre><code><button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#success-modal">
    {{translate('Success modal')}}
</button>
{{-- modal --}}
<div class="modal fade" id="success-modal" tabindex="-1" aria-labelledby="toggle-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header border-0 pb-0 d-flex justify-content-end">
                <button type="button" class="btn-close border-0 btn-circle bg-section2 shadow-none"
                    data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body px-20 py-0 mb-30">
                <div class="d-flex flex-column align-items-center text-center mb-30">
                    <img src="{{  dynamicAsset(path: 'public/assets/new/back-end/img/icons/modal-success-logo.png') }}"
                        width="80" class="mb-20" alt="">
                    <h2 class="modal-title mb-3">System File Uploaded Successfully!</h2>
                    <div class="text-center">Your chosen theme are successfully uploaded. If want to active this theme
                        click Active, or you can ignore.</div>
                </div>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-primary max-w-180 flex-grow-1"
                        data-bs-dismiss="modal">{{ translate('Okay, Got it') }}</button>
                </div>
            </div>
        </div>
    </div>
</div></code></pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- snippet container ends --}}
                </div>
            </div>
            <div class="col-lg-6">
                <div class="border border-dashed border-primary rounded p-3 w-auto mb-3">
                    <h3 class="text-primary mb-3">Modal toggle</h3>
                    {{-- snippet container --}}
                    <div class="component-snippets-container">
                        <div class="component-snippets-preview">
                            <div id="liveAlertPlaceholder">
                                <div></div>
                            </div>
                            <div>
                                <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#toggle-modal">
                                    {{translate('toggle modal')}}
                                </button>
                                {{-- modal --}}
                                <div class="modal fade" id="toggle-modal" tabindex="-1" aria-labelledby="toggle-modal" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content shadow-lg">
                                            <div class="modal-header border-0 pb-0 d-flex justify-content-end">
                                                <button type="button" class="btn-close border-0 btn-circle bg-section2 shadow-none"
                                                    data-bs-dismiss="modal" aria-label="Close">
                                                </button>
                                            </div>
                                            <div class="modal-body px-20 py-0 mb-30">
                                                <div class="d-flex flex-column align-items-center text-center mb-30">
                                                    <img src="{{  dynamicAsset(path: 'public/assets/new/back-end/img/icons/modal-success-logo.png') }}"
                                                        width="80" class="mb-20" id="toggle-modal-image" alt="">
                                                    <h2 class="modal-title mb-3" id="toggle-modal-title">Turn ON PayPal Payment Method</h2>
                                                    <div class="text-center" id="toggle-modal-message">Lorem ipsum dolor sit amet, consectetur
                                                        adipiscing elit. Aliquam odio tellus, laoreet </div>
                                                </div>
                                                <div class="d-flex justify-content-center gap-3">
                                                    <button type="button" class="btn btn-secondary max-w-120 flex-grow-1"
                                                        data-bs-dismiss="modal">{{ translate('cancel') }}</button>
                                                    <button type="button" class="btn btn-primary max-w-120 flex-grow-1" id="toggle-modal-ok-button"
                                                        data-bs-dismiss="modal">{{ translate('yes') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="position-relative snippets-code-hover">
                            <div class="component-snippets-code-header">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#html-tab-pane"
                                            type="button" role="tab" aria-controls="html-tab-pane" aria-selected="true">Html</button>
                                </ul>
                                <button class="btn btn-icon copy-button">
                                    <i class="fi fi-rr-copy"></i>
                                </button>
                            </div>
                            <div class="tab-content content position-absolute start-0 top-100 shadow-lg bg-white p-1 z-3 mw-100"
                                id="myTabContent">
                                <div class="tab-pane fade show active" id="html-tab-pane" role="tabpanel" aria-labelledby="home-tab"
                                    tabindex="0">
                                    <div class="component-snippets-code-container">
<pre><code><button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#toggle-modal">
    {{translate('toggle modal')}}
</button>
{{-- modal --}}
<div class="modal fade" id="toggle-modal" tabindex="-1" aria-labelledby="toggle-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header border-0 pb-0 d-flex justify-content-end">
                <button type="button" class="btn-close border-0 btn-circle bg-section2 shadow-none"
                    data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body px-20 py-0 mb-30">
                <div class="d-flex flex-column align-items-center text-center mb-30">
                    <img src="{{  dynamicAsset(path: 'public/assets/new/back-end/img/icons/modal-success-logo.png') }}"
                        width="80" class="mb-20" id="toggle-modal-image" alt="">
                    <h2 class="modal-title mb-3" id="toggle-modal-title">Turn ON PayPal Payment Method</h2>
                    <div class="text-center" id="toggle-modal-message">Lorem ipsum dolor sit amet, consectetur
                        adipiscing elit. Aliquam odio tellus, laoreet </div>
                </div>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-secondary max-w-120 flex-grow-1"
                        data-bs-dismiss="modal">{{ translate('cancel') }}</button>
                    <button type="button" class="btn btn-primary max-w-120 flex-grow-1" id="toggle-modal-ok-button"
                        data-bs-dismiss="modal">{{ translate('yes') }}</button>
                </div>
            </div>
        </div>
    </div>
</div></code></pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- snippet container ends --}}
                </div>
            </div>
                        <div class="col-lg-6">
                <div class="border border-dashed border-primary rounded p-3 w-auto mb-3">
                    <h3 class="text-primary mb-3">Modal delete</h3>
                    {{-- snippet container --}}
                    <div class="component-snippets-container">
                        <div class="component-snippets-preview">
                            <div id="liveAlertPlaceholder">
                                <div></div>
                            </div>
                            <div>
                                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    {{translate('delete modal')}}
                                </button>
                                {{-- modal --}}
                                <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header border-0 pb-0 d-flex justify-content-end">
                                                <button type="button" class="btn-close border-0 btn-circle bg-section2 shadow-none"
                                                    data-bs-dismiss="modal" aria-label="Close">
                                                </button>
                                            </div>
                                            <div class="modal-body px-20 py-0 mb-30">
                                                <div class="d-flex flex-column align-items-center text-center mb-30">
                                                    <img src="{{dynamicAsset(path: 'public/assets/new/back-end/img/modal/delete.png')}}" width="80"
                                                        class="mb-20" id="" alt="">
                                                    <h2 class="modal-title mb-3" id="">Want to delete this FAQ?</h2>
                                                    <div class="text-center" id="">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam odio
                                                        tellus, laoreet </div>
                                                </div>
                                                <div class="d-flex justify-content-center gap-3">
                                                    <button type="button" class="btn btn-secondary max-w-120 flex-grow-1"
                                                        data-bs-dismiss="modal">No</button>
                                                    <button type="button" class="btn btn-danger max-w-120 flex-grow-1" data-bs-dismiss="modal">Yes,
                                                        Delete</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="position-relative snippets-code-hover">
                            <div class="component-snippets-code-header">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#html-tab-pane"
                                            type="button" role="tab" aria-controls="html-tab-pane" aria-selected="true">Html</button>
                                </ul>
                                <button class="btn btn-icon copy-button">
                                    <i class="fi fi-rr-copy"></i>
                                </button>
                            </div>
                            <div class="tab-content content position-absolute start-0 top-100 shadow-lg bg-white p-1 z-3 mw-100"
                                id="myTabContent">
                                <div class="tab-pane fade show active" id="html-tab-pane" role="tabpanel" aria-labelledby="home-tab"
                                    tabindex="0">
                                    <div class="component-snippets-code-container">
<pre><code><button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
    {{translate('delete modal')}}
</button>
{{-- modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0 d-flex justify-content-end">
                <button type="button" class="btn-close border-0 btn-circle bg-section2 shadow-none"
                    data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body px-20 py-0 mb-30">
                <div class="d-flex flex-column align-items-center text-center mb-30">
                    <img src="{{dynamicAsset(path: 'public/assets/new/back-end/img/modal/delete.png')}}" width="80"
                        class="mb-20" id="" alt="">
                    <h2 class="modal-title mb-3" id="">Want to delete this FAQ?</h2>
                    <div class="text-center" id="">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam odio
                        tellus, laoreet </div>
                </div>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-secondary max-w-120 flex-grow-1"
                        data-bs-dismiss="modal">No</button>
                    <button type="button" class="btn btn-danger max-w-120 flex-grow-1" data-bs-dismiss="modal">Yes,
                        Delete</button>
                </div>
            </div>
        </div>
    </div>
</div></code></pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- snippet container ends --}}
                </div>
            </div>
            <div class="col-lg-6">
                <div class="border border-dashed border-primary rounded p-3 w-auto mb-3">
                    <h3 class="text-primary mb-3">Modal with input</h3>
                    {{-- snippet container --}}
                    <div class="component-snippets-container">
                        <div class="component-snippets-preview">
                            <div id="liveAlertPlaceholder">
                                <div></div>
                            </div>
                            <div>
                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#send-mail-confirmation-modal">
                                    <i class="fi fi-sr-paper-plane"></i>
                                    {{translate('send_test_mail')}}
                                </button>
                                {{-- modal --}}
                                <div class="modal fade" id="send-mail-confirmation-modal" tabindex="-1" aria-labelledby="send-mail-confirmation-modal"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered max-w-655">
                                        <div class="modal-content">
                                            <div class="modal-header border-0 pb-0 d-flex justify-content-end">
                                                <button type="button" class="btn-close border-0 btn-circle bg-section2 shadow-none"
                                                    data-bs-dismiss="modal" aria-label="Close">
                                                </button>
                                            </div>
                                            <div class="modal-body px-20 py-0 mb-30">
                                                <div class="d-flex flex-column gap-sm-20 gap-3">
                                                    <div>
                                                        <h3>Send Test Mail</h3>
                                                        <p class="fs-12 mb-0">Insert a valid email addresser to get mail</p>
                                                    </div>
                                                    <div
                                                        class="bg-info bg-opacity-10 fs-12 px-12 py-10 text-dark rounded d-flex gap-2 align-items-center">
                                                        <i class="fi fi-sr-lightbulb-on text-info"></i>
                                                        <span>
                                                            SMTP is configured for Mail. Please test to ensure you are receiving mail correctly.
                                                        </span>
                                                    </div>
                                                    <div
                                                        class="p-12 p-sm-20 bg-section rounded d-flex flex-wrap gap-sm-20 gap-3 justify-content-end align-items-end">
                                                        <div class="flex-grow-1">
                                                            <label class="form-label" for="">
                                                                Type Mail Address
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="email" id="test-email" class="form-control"
                                                                placeholder="{{translate('ex').':'.'jhon@email.com'}}">
                                                        </div>
                                                        <button type="button" id="test-mail-send"
                                                            class="btn btn-primary px-4 min-w-120 h-40">{{translate('send_mail')}}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="position-relative snippets-code-hover">
                            <div class="component-snippets-code-header">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#html-tab-pane"
                                            type="button" role="tab" aria-controls="html-tab-pane" aria-selected="true">Html</button>
                                </ul>
                                <button class="btn btn-icon copy-button">
                                    <i class="fi fi-rr-copy"></i>
                                </button>
                            </div>
                            <div class="tab-content content position-absolute start-0 top-100 shadow-lg bg-white p-1 z-3 mw-100"
                                id="myTabContent">
                                <div class="tab-pane fade show active" id="html-tab-pane" role="tabpanel" aria-labelledby="home-tab"
                                    tabindex="0">
                                    <div class="component-snippets-code-container">
<pre><code><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#send-mail-confirmation-modal">
    <i class="fi fi-sr-paper-plane"></i>
    {{translate('send_test_mail')}}
</button>
{{-- modal --}}
<div class="modal fade" id="send-mail-confirmation-modal" tabindex="-1" aria-labelledby="send-mail-confirmation-modal"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered max-w-655">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0 d-flex justify-content-end">
                <button type="button" class="btn-close border-0 btn-circle bg-section2 shadow-none"
                    data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body px-20 py-0 mb-30">
                <div class="d-flex flex-column gap-sm-20 gap-3">
                    <div>
                        <h3>Send Test Mail</h3>
                        <p class="fs-12 mb-0">Insert a valid email addresser to get mail</p>
                    </div>
                    <div
                        class="bg-info bg-opacity-10 fs-12 px-12 py-10 text-dark rounded d-flex gap-2 align-items-center">
                        <i class="fi fi-sr-lightbulb-on text-info"></i>
                        <span>
                            SMTP is configured for Mail. Please test to ensure you are receiving mail correctly.
                        </span>
                    </div>
                    <div
                        class="p-12 p-sm-20 bg-section rounded d-flex flex-wrap gap-sm-20 gap-3 justify-content-end align-items-end">
                        <div class="flex-grow-1">
                            <label class="form-label" for="">
                                Type Mail Address
                                <span class="text-danger">*</span>
                            </label>
                            <input type="email" id="test-email" class="form-control"
                                placeholder="{{translate('ex').':'.'jhon@email.com'}}">
                        </div>
                        <button type="button" id="test-mail-send"
                            class="btn btn-primary px-4 min-w-120 h-40">{{translate('send_mail')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div></code></pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- snippet container ends --}}
                </div>
            </div>
        </div>
    </div>
</div>