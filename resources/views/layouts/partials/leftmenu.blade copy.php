<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">

                <!-- @if (auth()->check() && auth()->user()->isSuperAdmin()) -->
                @if(auth()->user()->hasPermission('dashboard'))
                <li>
                    <a href="{{ route('admin.dashboard.index') }}" class="waves-effect">
                        <i class="bx bx-home-circle"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasPermission('earned'))
                <li>
                    <a href="{{ route('admin.dashboard.index') }}" class="waves-effect">
                        <i class="bx bx-rupee"></i>
                        <span>Earned</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasPermission('interactions'))
                <li>
                    <a href="{{ route('admin.interactions.index') }}" class="waves-effect">
                        <i class="bx bx-transfer"></i>
                        <span>Interactions</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasPermission('product_stock'))
                <li>
                    <a href="{{ route('admin.product_stocks.index') }}" class="waves-effect">
                        <i class="bx bx-package"></i>
                        <span>Product Stock</span>
                    </a>
                </li>
                @endif


                @if(
                auth()->user()->hasPermission('zodiac_signs') ||
                auth()->user()->hasPermission('horoscope')
                )
                <li class="menu-title">HOROSCOPE MAINTENANCE</li>
                @endif

                @if(auth()->user()->hasPermission('zodiac_signs'))
                <li>
                    <a href="{{ route('admin.zodiac_signs.index') }}">
                        <i class="bx bx-wind"></i>
                        <span>Zodiac Signs</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasPermission('horoscope'))
                <li>
                    <a href="{{ route('admin.horoscopes.index') }}">
                        <i class="bx bx-star"></i>
                        <span>Horoscope</span>
                    </a>
                </li>
                @endif


                @if(
                auth()->user()->hasPermission('astrologers') ||
                auth()->user()->hasPermission('users')
                )
                <li class="menu-title">Astrologers & Users</li>
                @endif

                @if(auth()->user()->hasPermission('astrologers'))
                <li>
                    <a href="{{ route('admin.astrologers.index') }}">
                        <i class="bx bx-user-circle"></i>
                        <span>Astrologers</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasPermission('users'))
                <li>
                    <a href="{{ route('admin.users.index') }}">
                        <i class="bx bx-group"></i>
                        <span>Users</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasPermission('payouts'))
                <li>
                    <a href="{{ route('admin.payouts.index') }}">
                        <i class="bx bx-money"></i>
                        <span>Payout Requests</span>
                    </a>
                </li>
                @endif


                @if(
                auth()->user()->hasPermission('blog_category') ||
                auth()->user()->hasPermission('blog')
                )
                <li class="menu-title">Blog</li>
                @endif

                @if(auth()->user()->hasPermission('blog_category'))
                <li>
                    <a href="{{ route('admin.blog_categories.index') }}">
                        Blog Category
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasPermission('blog'))
                <li>
                    <a href="{{ route('admin.blogs.index') }}">
                        Blog
                    </a>
                </li>
                @endif


                @if(
                auth()->user()->hasPermission('product_category') ||
                auth()->user()->hasPermission('product')
                )
                <li class="menu-title">Coupons & Products</li>
                @endif

                @if(auth()->user()->hasPermission('coupon'))
                <li>
                    <a href="{{ route('admin.coupons.index') }}">
                        Coupon
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasPermission('product_category'))
                <li>
                    <a href="{{ route('admin.product_categories.index') }}">
                        Product Category
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasPermission('product'))
                <li>
                    <a href="{{ route('admin.products.index') }}">
                        Product
                    </a>
                </li>
                @endif


                @if(
                auth()->user()->hasPermission('orders') ||
                auth()->user()->hasPermission('returns')
                )
                <li class="menu-title">ORDERS & RETURNS</li>
                @endif

                @if(auth()->user()->hasPermission('orders'))
                <li>
                    <a href="{{ route('admin.orders.index') }}">
                        Orders
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasPermission('returns'))
                <li>
                    <a href="{{ route('admin.returns.index') }}">
                        Returns
                    </a>
                </li>
                @endif


                @if(
                auth()->user()->hasPermission('astro_banner') ||
                auth()->user()->hasPermission('store_banner')
                )
                <li class="menu-title">Banners</li>
                @endif

                @if(auth()->user()->hasPermission('astro_banner'))
                <li>
                    <a href="{{ route('admin.astro_banners.index') }}">
                        Astro Banner
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasPermission('store_banner'))
                <li>
                    <a href="{{ route('admin.store_banners.index') }}">
                        Store Banner
                    </a>
                </li>
                @endif


                @if(auth()->user()->hasPermission('send_mail'))
                <li>
                    <a href="{{ route('admin.send_mail.index') }}">
                        Send Mail
                    </a>
                </li>
                @endif

            </ul>
        </div>
    </div>
</div>