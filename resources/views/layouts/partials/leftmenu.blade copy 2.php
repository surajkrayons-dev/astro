<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">

                @if (auth()->check() && auth()->user()->isSuperAdmin())
                    <li>
                        <a href="{{ route('admin.dashboard.index') }}" class="waves-effect">
                            <i class="bx bx-home-circle"></i>
                            <span key="t-chat">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.dashboard.index') }}" class="waves-effect">
                            <i class="bx bx-rupee"></i>
                            <span key="t-chat">Earned</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.interactions.index') }}" class="waves-effect">
                            <i class="bx bx-transfer"></i>
                            <span key="t-chat">Interactions</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.product_stocks.index') }}" class="waves-effect">
                            <i class="bx bx-package"></i>
                            <span key="t-chat">Product Stock</span>
                        </a>
                    </li>

                    <li class="menu-title" key="t-menu">HOROSCOPE MAINTENANCE</li>

                    <li>
                        <a href="{{ route('admin.zodiac_signs.index') }}">
                            <i class="bx bx-wind"></i>
                            <span>Zodiac Signs</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.horoscopes.index') }}">
                            <i class="bx bx-star"></i>
                            <span>Horoscope</span>
                        </a>
                    </li>

                    <li class="menu-title" key="t-menu">Astrologers & Users</li>
                    <li>
                        <a href="{{ route('admin.astrologers.index') }}" class="waves-effect">
                            <i class="bx bx-user-circle"></i>
                            <span key="t-chat">Astrologers</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.users.index') }}" class="waves-effect">
                            <i class="bx bx-group"></i>
                            <span key="t-chat">Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.payouts.index') }}" class="waves-effect">
                            <i class="bx bx-money"></i>
                            <span key="t-chat">Payout Requests</span>
                        </a>
                    </li>

                    <li class="menu-title" key="t-menu">Blog</li>
                    <li>
                        <a href="{{ route('admin.blog_categories.index') }}" class="waves-effect">
                            <i class="bx bx-layer"></i>
                            <span key="t-chat">Blog Category</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.blogs.index') }}" class="waves-effect">
                            <i class="bx bx-list-ul"></i>
                            <span key="t-chat">Blog</span>
                        </a>
                    </li>

                    <li class="menu-title" key="t-menu">Coupons & Products</li>
                    <li>
                        <a href="{{ route('admin.coupons.index') }}" class="waves-effect">
                            <i class="bx bxs-discount"></i>
                            <span key="t-chat">Coupon</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.product_categories.index') }}" class="waves-effect">
                            <i class="bx bx-box"></i>
                            <span key="t-chat">Product Category</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.products.index') }}" class="waves-effect">
                            <i class="bx bx-collection"></i>
                            <span key="t-chat">Product</span>
                        </a>
                    </li>

                    <li class="menu-title" key="t-menu">ORDERS & RETURNS</li>
                    <li>
                        <a href="{{ route('admin.orders.index') }}" class="waves-effect">
                            <i class="bx bx-cart"></i>
                            <span key="t-chat">Orders</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.returns.index') }}" class="waves-effect">
                            <i class="bx bx-undo"></i>
                            <span key="t-chat">Returns</span>
                        </a>
                    </li>

                    <li class="menu-title" key="t-menu">Banners</li>
                    <li>
                        <a href="{{ route('admin.astro_banners.index') }}" class="waves-effect">
                            <i class="bx bx-image"></i>
                            <span key="t-chat">Astro Banner</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.store_banners.index') }}" class="waves-effect">
                            <i class="bx bx-store"></i>
                            <span key="t-chat">Store Banner</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.send_mail.index') }}" class="waves-effect">
                            <i class="bx bx-envelope"></i>
                            <span>Send Mail</span>
                        </a>
                    </li>
                @endif

                <!--@if (auth()->check() && auth()->user()->isAstro())
-->

                <!--    <li class="menu-title">ASTROLOGER PANEL</li>-->

                <!--    <li>-->
                <!--        <a href="{{ route('astro.dashboard.index') }}">-->
                <!--            <i class="bx bx-home-circle"></i>-->
                <!--            <span>Dashboard</span>-->
                <!--        </a>-->
                <!--    </li>-->

                <!--    <li>-->
                <!--        <a href="#">-->
                <!--            <i class="bx bx-time"></i>-->
                <!--            <span>Availability</span>-->
                <!--        </a>-->
                <!--    </li>-->

                <!--    <li>-->
                <!--        <a href="#">-->
                <!--            <i class="bx bx-message-rounded-dots"></i>-->
                <!--            <span>Chat Requests</span>-->
                <!--        </a>-->
                <!--    </li>-->

                <!--    <li>-->
                <!--        <a href="#">-->
                <!--            <i class="bx bx-phone-call"></i>-->
                <!--            <span>Call Requests</span>-->
                <!--        </a>-->
                <!--    </li>-->

                <!--    <li>-->
                <!--        <a href="#">-->
                <!--            <i class="bx bxs-wallet"></i>-->
                <!--            <span>Wallet / Earnings</span>-->
                <!--        </a>-->
                <!--    </li>-->

                <!--    <li class="menu-title">REPORTS</li>-->

                <!--    <li>-->
                <!--        <a href="#">-->
                <!--            <i class="bx bx-history"></i>-->
                <!--            <span>Consultation History</span>-->
                <!--        </a>-->
                <!--    </li>-->

                <!--    <li>-->
                <!--        <a href="#">-->
                <!--            <i class="bx bxs-star"></i>-->
                <!--            <span>Ratings & Reviews</span>-->
                <!--        </a>-->
                <!--    </li>-->

                <!--
@endif-->

            </ul>
        </div>
    </div>
</div>
