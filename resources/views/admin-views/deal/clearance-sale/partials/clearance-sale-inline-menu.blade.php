@php
    use App\Enums\ViewPaths\Admin\ClearanceSale;
@endphp
{{-- <div class="inline-page-menu my-4">
    <ul class="list-unstyled">
        <li class="{{ Request::is('admin/deal/clearance-sale') ? 'active' : '' }}">
            <a href="{{ route('admin.deal.clearance-sale.index') }}">{{ translate('Manage_Inhouse_Offer') }}</a>
        </li>

        <li class="{{ Request::is('admin/deal/clearance-sale/' . ClearanceSale::VENDOR_OFFERS[URI]) ? 'active' : '' }}">
            <a href="{{ route('admin.deal.clearance-sale.vendor-offers') }}">{{ translate('Vendor_Offers') }}</a>
        </li>

        <li class="{{ Request::is('admin/deal/clearance-sale/' . ClearanceSale::PRIORITY_SETUP[URI]) ? 'active' : '' }}">
            <a href="{{ route('admin.deal.clearance-sale.priority-setup') }}">{{ translate('priority_setup') }}</a>
        </li>
    </ul>
</div> --}}

<div class="position-relative nav--tab-wrapper mb-4">
    <ul class="nav nav-pills nav--tab" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ Request::is('admin/deal/clearance-sale') ? 'active' : '' }}"
            href="{{ route('admin.deal.clearance-sale.index') }}"
            aria-selected="true"
            >
            {{ translate('Manage_Inhouse_Offer') }}
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ Request::is('admin/deal/clearance-sale/vendor-offers') ? 'active' : '' }}"
            href="{{ route('admin.deal.clearance-sale.vendor-offers') }}"
            aria-selected="true"
            >
            {{ translate('Manage_Vendor_Offer') }}
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ Request::is('admin/deal/clearance-sale/priority-setup') ? 'active' : '' }}"
            href="{{ route('admin.deal.clearance-sale.priority-setup') }}"
            aria-selected="true"
            >
            {{ translate('priority_setup') }}
            </a>
        </li>
    </ul>
    <div class="nav--tab__prev">
        <button class="btn btn-circle border-0 bg-white text-primary">
            <i class="fi fi-sr-angle-left"></i>
        </button>
    </div>
    <div class="nav--tab__next">
        <button class="btn btn-circle border-0 bg-white text-primary">
            <i class="fi fi-sr-angle-right"></i>
        </button>
    </div>

</div>
