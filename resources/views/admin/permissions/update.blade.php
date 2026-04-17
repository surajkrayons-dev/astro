@extends('layouts.master')

@section('title') Permissions @endsection

@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">
                Permissions Update - {{ $user->name }}
            </h4>

            <div class="page-title-right">
                <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
                    Back
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">

        <form method="POST" action="{{ route('admin.permissions.update', $user->id) }}">
            @csrf

            @php
            $permissions = json_decode($user->permissions ?? '[]');
            @endphp

            <div class="row">

                <!-- DASHBOARD -->
                <div class="col-md-3">
                    <label>
                        <input type="checkbox" name="permissions[]" value="dashboard"
                            {{ in_array('dashboard', $permissions) ? 'checked' : '' }}>
                        Dashboard
                    </label>
                </div>

                <!-- INTERACTIONS -->
                <div class="col-md-3">
                    <label>
                        <input type="checkbox" name="permissions[]" value="interactions"
                            {{ in_array('interactions', $permissions) ? 'checked' : '' }}>
                        Interactions
                    </label>
                </div>

                <!-- PRODUCT STOCK -->
                <div class="col-md-3">
                    <label>
                        <input type="checkbox" name="permissions[]" value="product_stocks"
                            {{ in_array('product_stocks', $permissions) ? 'checked' : '' }}>
                        Product Stock
                    </label>
                </div>

                <!-- ZODIAC -->
                <div class="col-md-3">
                    <label>
                        <input type="checkbox" name="permissions[]" value="zodiac_signs"
                            {{ in_array('zodiac_signs', $permissions) ? 'checked' : '' }}>
                        Zodiac Signs
                    </label>
                </div>

                <!-- HOROSCOPE -->
                <div class="col-md-3">
                    <label>
                        <input type="checkbox" name="permissions[]" value="horoscopes"
                            {{ in_array('horoscopes', $permissions) ? 'checked' : '' }}>
                        Horoscope
                    </label>
                </div>

                <!-- ASTROLOGERS -->
                <div class="col-md-3">
                    <label>
                        <input type="checkbox" name="permissions[]" value="astrologers"
                            {{ in_array('astrologers', $permissions) ? 'checked' : '' }}>
                        Astrologers
                    </label>
                </div>

                <!-- USERS -->
                <div class="col-md-3">
                    <label>
                        <input type="checkbox" name="permissions[]" value="users"
                            {{ in_array('users', $permissions) ? 'checked' : '' }}>
                        Users
                    </label>
                </div>

                <!-- PAYOUT -->
                <div class="col-md-3">
                    <label>
                        <input type="checkbox" name="permissions[]" value="payouts"
                            {{ in_array('payouts', $permissions) ? 'checked' : '' }}>
                        Payout Requests
                    </label>
                </div>

                <!-- BLOG CATEGORY -->
                <div class="col-md-3">
                    <label>
                        <input type="checkbox" name="permissions[]" value="blog_categories"
                            {{ in_array('blog_categories', $permissions) ? 'checked' : '' }}>
                        Blog Category
                    </label>
                </div>

                <!-- BLOG -->
                <div class="col-md-3">
                    <label>
                        <input type="checkbox" name="permissions[]" value="blogs"
                            {{ in_array('blogs', $permissions) ? 'checked' : '' }}>
                        Blog
                    </label>
                </div>

                <!-- COUPONS -->
                <div class="col-md-3">
                    <label>
                        <input type="checkbox" name="permissions[]" value="coupons"
                            {{ in_array('coupons', $permissions) ? 'checked' : '' }}>
                        Coupons
                    </label>
                </div>

                <!-- PRODUCT CATEGORY -->
                <div class="col-md-3">
                    <label>
                        <input type="checkbox" name="permissions[]" value="product_categories"
                            {{ in_array('product_categories', $permissions) ? 'checked' : '' }}>
                        Product Category
                    </label>
                </div>

                <!-- PRODUCTS -->
                <div class="col-md-3">
                    <label>
                        <input type="checkbox" name="permissions[]" value="products"
                            {{ in_array('products', $permissions) ? 'checked' : '' }}>
                        Products
                    </label>
                </div>

                <!-- ORDERS -->
                <div class="col-md-3">
                    <label>
                        <input type="checkbox" name="permissions[]" value="orders"
                            {{ in_array('orders', $permissions) ? 'checked' : '' }}>
                        Orders
                    </label>
                </div>

                <!-- RETURNS -->
                <div class="col-md-3">
                    <label>
                        <input type="checkbox" name="permissions[]" value="returns"
                            {{ in_array('returns', $permissions) ? 'checked' : '' }}>
                        Returns
                    </label>
                </div>

                <!-- BANNERS -->
                <div class="col-md-3">
                    <label>
                        <input type="checkbox" name="permissions[]" value="astro_banners"
                            {{ in_array('astro_banners', $permissions) ? 'checked' : '' }}>
                        Astro Banner
                    </label>
                </div>

                <div class="col-md-3">
                    <label>
                        <input type="checkbox" name="permissions[]" value="store_banners"
                            {{ in_array('store_banners', $permissions) ? 'checked' : '' }}>
                        Store Banner
                    </label>
                </div>

                <!-- MAIL -->
                <div class="col-md-3">
                    <label>
                        <input type="checkbox" name="permissions[]" value="send_mail"
                            {{ in_array('send_mail', $permissions) ? 'checked' : '' }}>
                        Send Mail
                    </label>
                </div>

            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-success">Save Permissions</button>
            </div>

        </form>

    </div>
</div>

@endsection