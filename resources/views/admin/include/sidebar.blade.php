<div class="app-sidebar colored">
    <div class="sidebar-header">
        <a class="header-brand d-block w-100 pl-5" href="{{route('admin.dashboard')}}">
            <div class="logo-img">
               <h4>Shiksha Light</h4>
            </div>
        </a>
        <div class="sidebar-action"><i class="ik ik-arrow-left-circle"></i></div>
        <button id="sidebarClose" class="nav-close"><i class="ik ik-x"></i></button>
    </div>

    @php
        $segment1 = request()->segment(1);
        $segment2 = request()->segment(2);
        $segment3 = request()->segment(3);
        $adminPage = ($segment1 === 'admin') ? $segment2 : $segment1;
    @endphp

    <div class="sidebar-content">
        <div class="nav-container">
            <nav id="main-menu-navigation" class="navigation-main">
                <div class="nav-item {{ $adminPage == 'dashboard' ? 'active' : '' }}">
                    <a href="{{route('admin.dashboard')}}"><i class="ik ik-bar-chart-2"></i><span>{{ __('Dashboard')}}</span></a>
                </div>
                {{-- <div class="nav-lavel">{{ __('Layouts')}} </div>
                <div class="nav-item {{ ($segment1 == 'pos') ? 'active' : '' }}">
                    <a href="{{url('inventory')}}"><i class="ik ik-shopping-cart"></i><span>{{ __('Inventory')}}</span> <span class=" badge badge-success badge-right">{{ __('New')}}</span></a>
                </div>
                <div class="nav-item {{ ($segment1 == 'pos') ? 'active' : '' }}">
                    <a href="{{url('pos')}}"><i class="ik ik-printer"></i><span>{{ __('POS')}}</span> <span class=" badge badge-success badge-right">{{ __('New')}}</span></a>
                </div> --> --}}
                {{-- @can('manage_user') --}}
                <div class="nav-item {{ in_array($adminPage, ['users', 'roles', 'permission', 'user']) ? 'active open' : '' }} has-sub">
                    <a href="#"><i class="ik ik-users"></i><span>{{ __('Administrator')}}</span></a>
                    <div class="submenu-content">
                        <!-- only those have manage_user permission will get access -->
                        {{-- @can('manage_user') --}}
                        <a href="{{url('users')}}" class="menu-item {{ $adminPage == 'users' ? 'active' : '' }}">{{ __('Users')}}</a>
                        <a href="{{url('user/create')}}" class="menu-item {{ $adminPage == 'user' && $segment3 == 'create' ? 'active' : '' }}">{{ __('Add User')}}</a>
                         {{-- @endcan --}}
                         <!-- only those have manage_role permission will get access -->
                        {{-- @can('manage_roles') --}}
                        {{-- <a href="{{url('roles')}}" class="menu-item {{ ($segment1 == 'roles') ? 'active' : '' }}">{{ __('Roles')}}</a> --}}
                        {{-- @endcan --}}
                        <!-- only those have manage_permission permission will get access -->
                        {{-- @can('manage_permission') --}}
                        {{-- <a href="{{url('permission')}}" class="menu-item {{ ($segment1 == 'permission') ? 'active' : '' }}">{{ __('Permission')}}</a> --}}
                        {{-- @endcan --}}
                    </div>
                </div>
                {{-- @endcan --}}

              <div class="nav-item {{ $adminPage == 'category' ? 'active' : '' }}">
                    <a href="{{route('admin.category')}}"><i class="ik ik-layers"></i><span>{{ __('Category')}}</span></a>
                </div>

                {{-- Product Module --}}
              <div class="nav-item {{ $adminPage == 'product' ? 'active open' : '' }} has-sub">
                    <a href="#"><i class="ik ik-package"></i><span>{{ __('Product')}}</span></a>
                  <div class="submenu-content">
                            <a href="{{route('admin.product.create')}}" class="menu-item {{ $adminPage == 'product' && $segment3 == 'create' ? 'active' : '' }}">{{ __('Create')}}</a>
                            <a href="{{route('admin.product')}}" class="menu-item {{ $adminPage == 'product' && (!$segment3 || $segment3 == 'get-list') ? 'active' : '' }}">{{ __('List')}}</a>
                            <a href="{{route('admin.product.recentlyDeletedPage')}}" class="menu-item {{ $adminPage == 'product' && $segment3 == 'recently-deleted-page' ? 'active' : '' }}">{{ __('Recently Deleted')}}</a>

                        </div>

               </div>

               {{-- banner --}}
              <div class="nav-item {{ $adminPage == 'banner' ? 'active' : '' }}">
                    <a href="{{route('admin.banner')}}"><i class="ik ik-image"></i><span>{{ __('Banner')}}</span></a>
                  {{-- <div class="submenu-content">
                            <a href="{{route('admin.banner')}}" class="menu-item {{ ($segment1 == 'customers') ? 'active' : '' }}">{{ __('List')}}</a>
                        </div> --}}

               </div>
               {{-- brand --}}
              <div class="nav-item {{ $adminPage == 'brand' ? 'active' : '' }}">
                    <a href="{{route('admin.brand')}}"><i class="ik ik-award"></i><span>{{ __('Brand')}}</span></a>
                  {{-- <div class="submenu-content">
                            <a href="{{route('admin.banner')}}" class="menu-item {{ ($segment1 == 'customers') ? 'active' : '' }}">{{ __('List')}}</a>
                        </div> --}}

               </div>


              <div class="nav-item {{ $adminPage == 'attribute' ? 'active' : '' }}">
                    <a href="{{route('admin.attribute')}}"><i class="ik ik-sliders"></i><span>{{ __('Attribute')}}</span></a>
               </div>

               {{-- Custom Field Type --}}
              <div class="nav-item {{ $adminPage == 'custom-field-type' ? 'active' : '' }}">
                    <a href="{{route('admin.custom-field-type.index')}}"><i class="ik ik-edit"></i><span>{{ __('Custom Field Type')}}</span></a>
                  {{-- <div class="submenu-content">
                            <a href="{{route('admin.unit.list')}}" class="menu-item {{ ($segment1 == 'customers') ? 'active' : '' }}">{{ __('List')}}</a>
                  </div> --}}
               </div> 

               {{-- Unit --}}
              <div class="nav-item {{ $adminPage == 'unit' ? 'active' : '' }}">
                    <a href="{{route('admin.unit.index')}}"><i class="ik ik-align-left"></i><span>{{ __('Unit')}}</span></a>
               </div>

                <div class="nav-item {{ $adminPage == 'product-code-type' || (strpos(request()->path(), 'product-code-type') !== false) ? 'active' : '' }}">
                    <a href="{{route('admin.product-code-type.index')}}"><i class="ik ik-hash"></i><span>{{ __('Product Code Type')}}</span></a>
               </div>

                {{-- Variant Name --}}
              <div class="nav-item {{ $adminPage == 'variant-name' ? 'active' : '' }}">
                    <a href="{{route('admin.variant-name.index')}}"><i class="ik ik-git-branch"></i><span>{{ __('Variant')}}</span></a>
               </div>


               {{-- Attribute Option --}}
              <div class="nav-item {{ $adminPage == 'attribute_option' ? 'active' : '' }}">
                    <a href="{{route('admin.attribute_option')}}"><i class="ik ik-list"></i><span>{{ __('Attribute Option')}}</span></a>
               </div>
               {{-- Color --}}
              <div class="nav-item {{ $adminPage == 'color' ? 'active' : '' }}">
                    <a href="{{route('admin.color')}}"><i class="ik ik-droplet"></i><span>{{ __('Color')}}</span></a>
               </div>

               <!-- Order -->
              <div class="nav-item {{ $adminPage == 'order' && !$segment3 ? 'active' : '' }}">
                    <a href="{{route('admin.order')}}"><i class="ik ik-shopping-cart"></i><span>{{ __('Order')}}</span></a>
               </div>

               <div class="nav-item {{ $adminPage == 'enquiries' ? 'active' : '' }}">
                    <a href="{{ route('admin.enquiries.index') }}"><i class="ik ik-mail"></i><span>{{ __('Enquiries')}}</span></a>
               </div>

               @php
                   $unreadContacts = \Illuminate\Support\Facades\Schema::hasTable('contact_messages')
                       ? \App\Models\ContactMessage::where('is_read', false)->count() : 0;
               @endphp
               <div class="nav-item {{ $adminPage == 'contact-messages' ? 'active' : '' }}">
                    <a href="{{ route('admin.contact-messages.index') }}">
                        <i class="ik ik-message-square"></i><span>{{ __('Contact Messages')}}</span>
                        @if($unreadContacts)<span class="badge badge-danger">{{ $unreadContacts }}</span>@endif
                    </a>
               </div>

               <div class="nav-item {{ in_array($adminPage, ['website-settings', 'smtp-settings', 'email-templates']) ? 'active open' : '' }} has-sub">
                    <a href="#"><i class="ik ik-globe"></i><span>{{ __('Website')}}</span></a>
                    <div class="submenu-content">
                        <a href="{{ route('admin.website-settings.edit') }}" class="menu-item {{ $adminPage == 'website-settings' ? 'active' : '' }}">{{ __('Settings')}}</a>
                        <a href="{{ route('admin.email-templates.index') }}" class="menu-item {{ $adminPage == 'email-templates' ? 'active' : '' }}">{{ __('Email Templates')}}</a>
                        <a href="{{ route('admin.smtp.edit') }}" class="menu-item {{ $adminPage == 'smtp-settings' ? 'active' : '' }}">{{ __('SMTP')}}</a>
                        <a href="{{ url('/') }}" target="_blank" class="menu-item">{{ __('View Website')}}</a>
                    </div>
               </div>
              {{-- <div class="nav-item {{ request()->routeIs('admin.order.salesmen') || request()->routeIs('admin.order.salesman') ? 'active' : '' }}">
                    <a href="{{ route('admin.order.salesmen') }}"><i class="ik ik-user-check"></i><span>{{ __('Salesman Orders')}}</span></a>
               </div> --}}
               <!-- End Order Section -->














                {{-- @can('manage_customers')
                    <div class="nav-item {{ ($segment1 == 'customer' || $segment1 == 'customers') ? 'active open' : '' }} has-sub">
                        <a href="#"><i class="ik ik-users"></i><span>{{ __('Customers')}}</span></a>
                        <div class="submenu-content">
                            <a href="{{route('admin.customer.create')}}" class="menu-item {{ ($segment1 == 'customer') ? 'active' : '' }}">{{ __('Create')}}</a>
                            <a href="{{url('customers')}}" class="menu-item {{ ($segment1 == 'customers') ? 'active' : '' }}">{{ __('List')}}</a>
                        </div>
                    </div>
                @endcan --}}














                {{-- This is inbuilt sidebar --}}

                {{-- <div class="nav-item {{ ($segment1 == 'file-manager') ? 'active open' : '' }}">
                    <a href="{{route('admin.getMediaManager')}}"><i class="ik ik-upload"></i><span>{{ __('Upload Media')}}</span></a>
                </div> --}}

                {{-- @can('manage_customers')
                    <div class="nav-item {{ ($segment1 == 'customer' || $segment1 == 'customers') ? 'active open' : '' }} has-sub">
                        <a href="#"><i class="ik ik-users"></i><span>{{ __('Customers')}}</span></a>
                        <div class="submenu-content">
                            <a href="{{route('admin.customer.create')}}" class="menu-item {{ ($segment1 == 'customer') ? 'active' : '' }}">{{ __('Create')}}</a>
                            <a href="{{url('customers')}}" class="menu-item {{ ($segment1 == 'customers') ? 'active' : '' }}">{{ __('List')}}</a>
                        </div>
                    </div>
                @endcan --}}

                {{-- @can('manage_products')
                    <div class="nav-item has-sub">
                        <a href="#"><i class="ik ik-box"></i><span>{{ __('Products')}}</span></a>
                        <div class="submenu-content">
                            <a href="#" class="menu-item ">{{ __('Create')}}</a>
                            <a href="#" class="menu-item ">{{ __('List')}}</a>
                        </div>
                    </div>
                @endcan --}}

                {{-- @can('manage_booking')
                    <div class="nav-item has-sub">
                        <a href="#"><i class="ik ik-edit"></i><span>{{ __('Bookings')}}</span></a>
                        <div class="submenu-content">
                            <a href="#" class="menu-item ">{{ __('Create')}}</a>
                            <a href="#" class="menu-item ">{{ __('List')}}</a>
                        </div>
                    </div>
                @endcan --}}

                {{-- @can('manage_brand')
                    <div class="nav-item {{ ($segment1.'/'.$segment2 == 'brand/create' || $segment1.'/'.$segment2 == 'brands/list') ? 'active open' : '' }} has-sub">
                        <a href="#"><i class="ik ik-archive"></i><span>{{ __('Brand')}}</span></a>
                        <div class="submenu-content">
                            <a href="{{route('brand.create')}}" class="menu-item {{ ($segment1.'/'.$segment2 == 'brand/create') ? 'active' : '' }}">{{ __('Create')}}</a>
                            <a href="{{route('brand.list')}}" class="menu-item {{ ($segment1.'/'.$segment2 == 'brands/list') ? 'active' : '' }}">{{ __('List')}}</a>
                        </div>
                    </div>
                @endcan --}}

                {{-- @can('manage_unit')
                    <div class="nav-item {{ ($segment1.'/'.$segment2 == 'unit/create' || $segment1.'/'.$segment2 == 'units/list') ? 'active open' : '' }} has-sub">
                        <a href="#"><i class="ik ik-clipboard"></i><span>{{ __('Unit')}}</span></a>
                        <div class="submenu-content">
                            <a href="{{route('unit.create')}}" class="menu-item {{ ($segment1.'/'.$segment2 == 'unit/create') ? 'active' : '' }}">{{ __('Create')}}</a>
                            <a href="{{route('unit.list')}}" class="menu-item {{ ($segment1.'/'.$segment2 == 'units/list') ? 'active' : '' }}">{{ __('List')}}</a>
                        </div>
                    </div>
                @endcan --}}




                {{-- @can('manage_booking')
                    <div class="nav-item {{ ($segment1.'/'.$segment2 == 'attribute/create' || $segment1.'/'.$segment2 == 'attribute/list' ||$segment1.'/'.$segment2 == 'attribute/edit') ? 'active open' : '' }} has-sub">
                        <a href="#"><i class="ik ik-shopping-bag"></i><span>{{ __('Attribute')}}</span></a>
                        <div class="submenu-content">
                            <a  href="{{route('attribute.create')}}" class="menu-item {{ ($segment1.'/'.$segment2 == 'attribute/create') ? 'active' : '' }}">{{ __('Create')}}</a>
                            <a href="{{route('attribute.list')}}" class="menu-item {{ ($segment1.'/'.$segment2 == 'attribute/list') ? 'active' : '' }}">{{ __('List')}}</a>
                        </div>
                    </div>
                @endcan --}}




                {{--<div class="nav-lavel">{{ __('Documentation')}} </div>
                <div class="nav-item {{ ($segment1 == 'rest-api') ? 'active' : '' }}">
                    <a href="{{url('rest-api')}}"><i class="ik ik-cloud"></i><span>{{ __('REST API')}}</span> </a>
                </div>
                <div class="nav-item {{ ($segment1 == 'permission-example') ? 'active' : '' }}">
                    <a href="{{url('permission-example')}}"><i class="ik ik-unlock"></i><span>{{ __('Laravel Permission')}}</span> </a>
                </div>
                <div class="nav-item {{ ($segment1 == 'table-datatable-edit') ? 'active' : '' }}">
                    <a href="{{url('table-datatable-edit')}}"><i class="ik ik-layout"></i><span>{{ __('Editable Datatable')}}</span>  </a>

                </div>
                <!-- end inventory pages -->

                <div class="nav-lavel">{{ __('Themekit Pages')}} </div>
                <div class="nav-item {{ ($segment1 == 'form-components' || $segment1 == 'form-advance'||$segment1 == 'form-addon') ? 'active open' : '' }} has-sub">
                    <a href="#"><i class="ik ik-edit"></i><span>{{ __('Forms')}}</span></a>
                    <div class="submenu-content">
                        <a href="{{url('form-components')}}" class="menu-item {{ ($segment1 == 'form-components') ? 'active' : '' }}">{{ __('Components')}}</a>
                        <a href="{{url('form-addon')}}" class="menu-item {{ ($segment1 == 'form-addon') ? 'active' : '' }}">{{ __('Add-On')}}</a>
                        <a href="{{url('form-advance')}}" class="menu-item {{ ($segment1 == 'form-advance') ? 'active' : '' }}">{{ __('Advance')}}</a>
                    </div>
                </div>
                <div class="nav-item {{ ($segment1 == 'form-picker') ? 'active' : '' }}">
                    <a href="{{url('form-picker')}}"><i class="ik ik-terminal"></i><span>{{ __('Form Picker')}}</span> </a>
                </div>

                <div class="nav-item {{ ($segment1 == 'table-bootstrap') ? 'active' : '' }}">
                    <a href="{{url('table-bootstrap')}}"><i class="ik ik-credit-card"></i><span>{{ __('Bootstrap Table')}}</span></a>
                </div>
                <div class="nav-item {{ ($segment1 == 'table-datatable') ? 'active' : '' }}">
                    <a href="{{url('table-datatable')}}"><i class="ik ik-inbox"></i><span>{{ __('Data Table')}}</span></a>
                </div>
                <div class="nav-item {{ ($segment1 == 'navbar') ? 'active' : '' }}">
                    <a href="{{url('navbar')}}"><i class="ik ik-menu"></i><span>{{ __('Navigation')}}</span> </a>
                </div>
                <div class="nav-item {{ ($segment1 == 'widgets' || $segment1 == 'widget-statistic'||$segment1 == 'widget-data'||$segment1 == 'widget-chart') ? 'active open' : '' }} has-sub">
                    <a href="javascript:void(0)"><i class="ik ik-layers"></i><span>{{ __('Widgets')}}</span> <span class="badge badge-danger">{{ __('150+')}}</span></a>
                    <div class="submenu-content">
                        <a href="{{url('widgets')}}" class="menu-item {{ ($segment1 == 'widgets') ? 'active' : '' }}">{{ __('Basic')}}</a>
                        <a href="{{url('widget-statistic')}}" class="menu-item {{ ($segment1 == 'widget-statistic') ? 'active' : '' }}">{{ __('Statistic')}}</a>
                        <a href="{{url('widget-data')}}" class="menu-item {{ ($segment1 == 'widget-data') ? 'active' : '' }}">{{ __('Data')}}</a>
                        <a href="{{url('widget-chart')}}" class="menu-item {{ ($segment1 == 'widget-chart') ? 'active' : '' }}">{{ __('Chart Widget')}}</a>
                    </div>
                </div>
                <div class="nav-item {{ ($segment1 == 'alerts' || $segment1 == 'buttons'||$segment1 == 'badges'||$segment1 == 'navigation') ? 'active open' : '' }} has-sub">
                    <a href="#"><i class="ik ik-box"></i><span>{{ __('Basic')}}</span></a>
                    <div class="submenu-content">
                        <a href="{{url('alerts')}}" class="menu-item {{ ($segment1 == 'alerts') ? 'active' : '' }}">{{ __('Alerts')}}</a>
                        <a href="{{url('badges')}}" class="menu-item {{ ($segment1 == 'badges') ? 'active' : '' }}">{{ __('Badges')}}</a>
                        <a href="{{url('buttons')}}" class="menu-item {{ ($segment1 == 'buttons') ? 'active' : '' }}">{{ __('Buttons')}}</a>
                        <a href="{{url('navigation')}}" class="menu-item {{ ($segment1 == 'navigation') ? 'active' : '' }}">{{ __('Navigation')}}</a>
                    </div>
                </div>
                <div class="nav-item {{ ($segment1 == 'modals' || $segment1 == 'notifications'||$segment1 == 'carousel'||$segment1 == 'range-slider' ||$segment1 == 'rating') ? 'active open' : '' }} has-sub">
                    <a href="#"><i class="ik ik-gitlab"></i><span>{{ __('Advance')}}</span> </a>
                    <div class="submenu-content">
                        <a href="{{url('modals')}}" class="menu-item {{ ($segment1 == 'modals') ? 'active' : '' }}">{{ __('Modals')}}</a>
                        <a href="{{url('notifications')}}" class="menu-item {{ ($segment1 == 'notifications') ? 'active' : '' }}" >{{ __('Notifications')}}</a>
                        <a href="{{url('carousel')}}" class="menu-item {{ ($segment1 == 'carousel') ? 'active' : '' }}">{{ __('Slider')}}</a>
                        <a href="{{url('range-slider')}}" class="menu-item {{ ($segment1 == 'range-slider') ? 'active' : '' }}">{{ __('Range Slider')}}</a>
                        <a href="{{url('rating')}}" class="menu-item {{ ($segment1 == 'rating') ? 'active' : '' }}">{{ __('Rating')}}</a>
                    </div>
                </div>


                <div class="nav-item {{ ($segment1 == 'charts-chartist' || $segment1 == 'charts-flot'||$segment1 == 'charts-knob'||$segment1 == 'charts-amcharts') ? 'active open' : '' }} has-sub">
                    <a href="#"><i class="ik ik-pie-chart"></i><span>{{ __('Charts')}}</span> </a>
                    <div class="submenu-content">
                        <a href="{{url('charts-chartist')}}" class="menu-item {{ ($segment1 == 'charts-chartist') ? 'active' : '' }}">{{ __('Chartist')}}</a>
                        <a href="{{url('charts-flot')}}" class="menu-item {{ ($segment1 == 'charts-flot') ? 'active' : '' }}">{{ __('Flot')}}</a>
                        <a href="{{url('charts-knob')}}" class="menu-item {{ ($segment1 == 'charts-knob') ? 'active' : '' }}">{{ __('Knob')}}</a>
                        <a href="{{url('charts-amcharts')}}" class="menu-item {{ ($segment1 == 'charts-amcharts') ? 'active' : '' }}">{{ __('Amcharts')}}</a>
                    </div>
                </div>
                <div class="nav-item {{ ($segment1 == 'calendar') ? 'active' : '' }}">
                    <a href="{{url('calendar')}}"><i class="ik ik-calendar"></i><span>{{ __('Calendar')}}</span></a>
                </div>
                <div class="nav-item {{ ($segment1 == 'taskboard') ? 'active' : '' }}">
                    <a href="{{url('taskboard')}}"><i class="ik ik-server"></i><span>{{ __('Taskboard')}}</span></a>
                </div>

                <div class="nav-item {{ ($segment1 == 'login-1' || $segment1 == 'register'||$segment1 == 'forgot-password') ? 'active open' : '' }} has-sub">
                    <a href="#"><i class="ik ik-lock"></i><span>{{ __('Authentication')}}</span></a>
                    <div class="submenu-content">
                        <a href="{{url('login-1')}}" class="menu-item {{ ($segment1 == 'login-1') ? 'active' : '' }}">{{ __('Login')}}</a>
                        <a href="{{url('register')}}" class="menu-item {{ ($segment1 == 'register-1') ? 'active' : '' }}">{{ __('Register')}}</a>
                        <a href="{{url('forgot-password')}}" class="menu-item {{ ($segment1 == 'forgot-password') ? 'active' : '' }}">{{ __('Forgot Password')}}</a>
                    </div>
                </div>

                <div class="nav-item {{ ($segment1 == 'profile' || $segment1 == 'invoice'||$segment1 == 'session-timeout') ? 'active open' : '' }} has-sub">
                    <a href="#"><i class="ik ik-file-text"></i><span>{{ __('Pages')}}</span></a>
                    <div class="submenu-content">
                        <a href="{{url('profile')}}" class="menu-item {{ ($segment1 == 'profile') ? 'active' : '' }}">{{ __('Profile')}}</a>
                        <a href="{{url('invoice')}}" class="menu-item {{ ($segment1 == 'invoice') ? 'active' : '' }}">{{ __('Invoice')}}</a>
                        <a href="{{url('project')}}" class="menu-item {{ ($segment1 == 'project') ? 'active' : '' }}">{{ __('Project')}}</a>
                        <a href="{{url('view')}}" class="menu-item {{ ($segment1 == 'view') ? 'active' : '' }}">{{ __('View')}}</a>
                        <a href="{{url('session-timeout')}}" class="menu-item {{ ($segment1 == 'session-timeout') ? 'active' : '' }}">{{ __('Session Timeout')}}</a>
                    </div>
                </div>
                <div class="nav-item {{ ($segment1 == 'layouts') ? 'active' : '' }}">
                    <a href="{{url('layouts')}}"><i class="ik ik-layout"></i><span>{{ __('Layouts')}}</span></a>
                </div>--}}
                {{-- <div class="nav-item {{ ($segment1 == 'icons') ? 'active' : '' }}">
                    <a href="{{url('icons')}}"><i class="ik ik-command"></i><span>{{ __('Icons')}}</span></a>
                </div> --}}
                {{--<div class="nav-item {{ ($segment1 == 'pricing') ? 'active' : '' }}">
                    <a href="{{url('pricing')}}"><i class="ik ik-dollar-sign"></i><span>{{ __('Pricing')}}</span></a>
                </div>
                <div class="nav-item has-sub">
                    <a href="javascript:void(0)"><i class="ik ik-list"></i><span>{{ __('Menu Levels')}}</span></a>
                    <div class="submenu-content">
                        <a href="javascript:void(0)" class="menu-item">{{ __('Menu Level 2.1')}}</a>
                        <div class="nav-item {{ ($segment1 == '') ? 'active' : '' }} has-sub">
                            <a href="javascript:void(0)" class="menu-item">{{ __('Menu Level 2.2')}}</a>
                            <div class="submenu-content">
                                <a href="javascript:void(0)" class="menu-item">{{ __('Menu Level 3.1')}}</a>
                            </div>
                        </div>
                        <a href="javascript:void(0)" class="menu-item">{{ __('Menu Level 2.3')}}</a>
                    </div>
                </div>
                <div class="nav-item">
                    <a href="javascript:void(0)" class="disabled"><i class="ik ik-slash"></i><span>{{ __('Disabled Menu')}}</span></a>
                </div>--}}

        </div>
    </div>
</div>
