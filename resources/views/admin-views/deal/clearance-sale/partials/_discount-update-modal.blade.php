<div class="modal fade" id="discount-update-modal">
    <div class="modal-dialog">
        <div class="modal-content" style="max-height: 80vh; overflow-y: auto;">
            <div class="modal-header border-0 pb-0 d-flex justify-content-end">
                <button type="button" class="btn-close border-0 btn-circle bg-section2 shadow-none"
                    data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('admin.deal.clearance-sale.update-discount')}}" method="post" class="discount-amount-update">
                    @csrf
                    <input type="hidden" name="product_id">
                    <input type="hidden" name="id">
                    <div class="mb-30">
                        <a href="" class="text-dark text-hover-primary d-flex align-items-center gap-2">
                            <img src="{{ asset('public/assets/back-end/img/160x160/img2.jpg') }}"
                                 class="rounded border" alt="" width="60">
                            <h6 class="fs-14 fw-medium">
                                {{ translate('Family Size Trolley Case Long Lasting and 8 Wheel Waterproof Travel bag') }}

                            </h6>
                        </a>
                        <div class="mt-30">
                            <label
                                class="form-label fw-medium">{{ translate('Discount Amount') }}
                                <span id="discount-symbol">(%)</span>
                            </label>
                            <div class="input-group">
                                <input type="text" name="discount_amount" class="form-control" placeholder="Ex : 10" placeholder="">
                                <div class="input-group-append select-wrapper">
                                    <input type="hidden" id="dynamic-currency-symbol" value="{{ getCurrencySymbol(currencyCode: getCurrencyCode()) }}">
                                    <select name="discount_type" id="discount_type" class="form-select shadow-none h-auto">
                                        <option value="percentage">%</option>
                                        <option value="flat">{{ getCurrencySymbol(currencyCode: getCurrencyCode()) }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-3 align-items-center justify-content-end">
                        <button class="btn text-danger bg-danger bg-opacity-10 border-0 fw-semibold" data-bs-dismiss="modal"
                                type="reset">{{ translate('Cancel') }}</button>
                        <button class="btn btn-primary fw-semibold discount-amount-submit"
                                type="button">{{ translate('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
