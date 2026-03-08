<div class="modal fade" id="refundModal{{$id}}" tabindex="-1" aria-labelledby="refundModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header px-sm-5">
                <h1 class="modal-title fs-5" id="refundModalLabel">{{translate('Refund_Request')}}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-sm-5">
                <div class="d-flex flex-column flex-sm-row flex-wrap gap-4 justify-content-between mb-4">
                    <div class="media align-items-center gap-3">
                        <div class="avatar avatar-xxl rounded border overflow-hidden">
                            <img class="d-block img-fit-contain"
                                 src="{{ getStorageImages(path: $detail?->productAllStatus?->thumbnail_full_url, type: 'product') }}"
                                 alt="" width="60">
                        </div>
                        <div class="media-body d-flex gap-1 flex-column">
                            <h6 class="text-truncate width--20ch">
                                <h6>
                                    <a href="{{route('product',[$product['slug']])}}">
                                        {{isset($product['name']) ? Str::limit($product['name'],40) : ''}}
                                    </a>
                                    @if($order_details->refund_request == 1)
                                        <small> ({{translate('refund_pending')}}) </small> <br>
                                    @elseif($order_details->refund_request == 2)
                                        <small> ({{translate('refund_approved')}}) </small> <br>
                                    @elseif($order_details->refund_request == 3)
                                        <small> ({{translate('refund_rejected')}}) </small> <br>
                                    @elseif($order_details->refund_request == 4)
                                        <small> ({{translate('refund_refunded')}}) </small> <br>
                                    @endif<br>
                                </h6>
                                @if($order_details->variant)
                                    <small>{{translate('variant')}} :{{$order_details->variant}} </small>
                                @endif
                            </h6>
                        </div>
                    </div>
                    @php($refundDetailsSummery = \App\Utils\OrderManager::getRefundDetailsForSingleOrderDetails(orderDetailsId: $order_details['id']))
                    <div class="d-flex flex-column gap-1 fs-12">
                        <span>{{ translate('Qty')}} : {{$order_details->qty}}</span>
                        <span>{{ translate('Price')}} : {{webCurrencyConverter($order_details->price)}}</span>
                        <span>{{ translate('Discount')}} : {{webCurrencyConverter($order_details->discount)}}</span>
                        <span>{{ translate('Tax')}} : {{webCurrencyConverter($order_details->tax)}}</span>
                    </div>

                    <div class="d-flex flex-column gap-1 fs-12">
                        <span>
                            {{ translate('Subtotal') }} : {{webCurrencyConverter($refundDetailsSummery['sub_total']) }}
                        </span>
                        <span>
                            {{ translate('Coupon_Discount') }} : {{webCurrencyConverter($refundDetailsSummery['coupon_discount']) }}
                        </span>
                        <span>
                            {{ translate('referral_Discount') }} : {{webCurrencyConverter($refundDetailsSummery['referral_discount']) }}
                        </span>
                        <span>
                            {{ translate('Total_Refundable_Amount') }} : {{ webCurrencyConverter($refundDetailsSummery['total_refundable_amount']) }}
                        </span>
                    </div>
                </div>

                <form action="{{ route('refund-store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-4">
                        <input type="hidden" name="order_details_id" value="{{$order_details->id}}">
                        <input type="hidden" name="amount" value="{{ $refundDetailsSummery['total_refundable_amount'] }}">
                        <label for="comment">{{translate('refund_reason')}}</label>
                        <textarea name="refund_reason" class="form-control" rows="4"
                                  placeholder="{{  translate('refund_reason')}}" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>{{translate('Attachment')}}</label>
                        <div class="d-flex flex-column gap-3">
                            <div class="row coba_refund"></div>
                            <div class="text-muted">{{translate('file_type').':'.'.jpg,.jpeg,.png'.translate('maximum_size').':'.'2MB'}}</div>
                        </div>
                    </div>
                    <div class="modal-footer mb-4 gap-3 px-sm-5 pb-4">
                        <button type="button" class="btn btn-secondary m-0"
                                data-bs-dismiss="modal">{{translate('Cancel')}}</button>
                        <button type="submit" class="btn btn-primary m-0">{{translate('Submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
