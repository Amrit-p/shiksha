@extends('admin.layouts.main') 
@section('title', 'Dashboard')
@section('content')
    <!-- push external head elements to head -->
    @push('head')

        <link rel="stylesheet" href="{{ asset('plugins/weather-icons/css/weather-icons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/owl.carousel/dist/assets/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/owl.carousel/dist/assets/owl.theme.default.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/chartist/dist/chartist.min.css') }}">
    @endpush

    <div class="container-fluid">
    	<div class="row">
    		<!-- page statustic chart start -->
            <div class="col-xl-3 col-md-6">
                <div class="card card-red text-white">
                    <div class="card-block">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h4 class="mb-0">{{$totalProducts}}</h4>
                                <p class="mb-0">{{ __('Products')}}</p>
                            </div>
                            <div class="col-4 text-right">
                                <i class="fas fa-cube f-30"></i>
                            </div>
                        </div>
                        <div id="Widget-line-chart1" class="chart-line chart-shadow"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-blue text-white">
                    <div class="card-block">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h4 class="mb-0">{{ $totalOrders }}</h4>
                                <p class="mb-0">{{ __('Orders')}}</p>
                            </div>
                            <div class="col-4 text-right">
                                <i class="ik ik-shopping-cart f-30"></i>
                            </div>
                        </div>
                        <div id="Widget-line-chart2" class="chart-line chart-shadow" ></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-green text-white">
                    <div class="card-block">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h4 class="mb-0">{{$totalCustomers}}</h4>
                                <p class="mb-0">{{ __('Customers')}}</p>
                            </div>
                            <div class="col-4 text-right">
                                <i class="ik ik-user f-30"></i>
                            </div>
                        </div>
                        <div id="Widget-line-chart3" class="chart-line chart-shadow"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-yellow text-white">
                    <div class="card-block">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h4 class="mb-0">{{$totalSales}}</h4>
                                <p class="mb-0">{{ __('Sales')}}</p>
                            </div>
                            <div class="col-4 text-right">
                                <i class="ik f-30">৳</i>
                            </div>
                        </div>
                        <div id="Widget-line-chart4" class="chart-line chart-shadow" ></div>
                    </div>
                </div>
            </div>
            <!-- page statustic chart end -->

            <div class="col-xl-3 col-md-6">
                <div class="card card-blue text-white">
                    <div class="card-block">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h4 class="mb-0">{{ $totalEnquiries ?? 0 }}</h4>
                                <p class="mb-0">Total Enquiries</p>
                            </div>
                            <div class="col-4 text-right">
                                <i class="ik ik-mail f-30"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-green text-white">
                    <div class="card-block">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h4 class="mb-0">{{ $todayEnquiries ?? 0 }}</h4>
                                <p class="mb-0">Today's Enquiries</p>
                            </div>
                            <div class="col-4 text-right">
                                <i class="ik ik-calendar f-30"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-yellow text-white">
                    <div class="card-block">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h4 class="mb-0">{{ $weekEnquiries ?? 0 }}</h4>
                                <p class="mb-0">This Week</p>
                            </div>
                            <div class="col-4 text-right">
                                <i class="ik ik-activity f-30"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-red text-white">
                    <div class="card-block">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h4 class="mb-0">{{ $monthEnquiries ?? 0 }}</h4>
                                <p class="mb-0">This Month</p>
                            </div>
                            <div class="col-4 text-right">
                                <i class="ik ik-bar-chart-2 f-30"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(!empty($enquiryStatusCounts) && $enquiryStatusCounts->count())
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h3>Enquiry Status Breakdown</h3></div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($enquiryStatusCounts as $status)
                                <div class="col-md-3 mb-3">
                                    <div class="p-3 border rounded">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge" style="background:{{ $status->color }};color:#fff;">{{ $status->name }}</span>
                                            <strong>{{ $status->enquiries_count }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <a href="{{ route('admin.enquiries.index') }}" class="btn btn-primary btn-sm">Manage Enquiries</a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Low Stock / Restock list -->
            @if (($lowStockSimpleProducts ?? collect())->isNotEmpty() || ($lowStockVariantAttributes ?? collect())->isNotEmpty())
            <div class="col-12">
                <div class="card table-card border-warning">
                    <div class="card-header bg-light">
                        <h3 class="mb-0">
                            <i class="ik ik-alert-triangle text-warning"></i> Low Stock — Restock Required
                        </h3>
                        <div class="card-header-right">
                            <ul class="list-unstyled card-option">
                                <li><i class="ik ik-chevron-left action-toggle"></i></li>
                                <li><i class="ik ik-minus minimize-card"></i></li>
                                <li><i class="ik ik-x close-card"></i></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-block">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Product') }}</th>
                                        <th>{{ __('Variant / Option') }}</th>
                                        <th>{{ __('Current Stock') }}</th>
                                        <th>{{ __('Min Stock') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lowStockSimpleProducts ?? [] as $p)
                                    <tr>
                                        <td>{{ $p->title }}</td>
                                        <td><span class="text-muted">— Simple product</span></td>
                                        <td><strong class="text-danger">{{ $p->stock }}</strong></td>
                                        <td>{{ $p->min_stock }}</td>
                                        <td>
                                            <a href="{{ route('admin.product.edit', $p->id) }}" class="btn btn-sm btn-primary">
                                                <i class="ik ik-edit"></i> Edit & restock
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @foreach ($lowStockVariantAttributes ?? [] as $va)
                                    <tr>
                                        <td>{{ $va->variant->product->title ?? '—' }}</td>
                                        <td>
                                            @if ($va->variant->variantname)
                                                <strong>{{ $va->variant->variantname->name }}</strong>
                                                @if ($va->option)
                                                    → {{ $va->option->name }}
                                                @endif
                                            @else
                                                {{ $va->option->name ?? 'Option #'.$va->id }}
                                            @endif
                                        </td>
                                        <td><strong class="text-danger">{{ $va->stock }}</strong></td>
                                        <td>{{ $va->min_stock }}</td>
                                        <td>
                                            <a href="{{ route('admin.product.edit', $va->variant->product_id) }}" class="btn btn-sm btn-primary">
                                                <i class="ik ik-edit"></i> Edit & restock
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="text-muted small mb-0 mt-2">
                            Set <strong>Min stock</strong> on product edit (simple product or per variant option) to get alerts when stock goes below that level.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- sale 2 card start -->
            {{--<div class="col-md-6 col-xl-4">
                <div class="card sale-card">
                    <div class="card-header">
                        <h3>{{ __('Realtime Profit')}}</h3>
                    </div>
                    <div class="card-block text-center">
                        <div id="realtime-profit"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card sale-card">
                    <div class="card-header">
                        <h3>{{ __('Sales Difference')}}</h3>
                    </div>
                    <div class="card-block text-center">
                        <div id="sale-diff" class="chart-shadow"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-xl-4">
                <div class="card card-green text-white">
                    <div class="card-block pb-0">
                        <div class="row mb-50">
                            <div class="col">
                                <h6 class="mb-5">{{ __('Sales In July')}}</h6>
                                <h5 class="mb-0  fw-700">{{ __('$2665.00')}}</h5>
                            </div>
                            <div class="col-auto text-center">
                                <p class="mb-5">{{ __('Direct Sale')}}</p>
                                <h6 class="mb-0">{{ __('$1768')}}</h6>
                            </div>

                            <div class="col-auto text-center">
                                <p class="mb-5">{{ __('Referal')}}</p>
                                <h6 class="mb-0">{{ __('$897')}}</h6>
                            </div>
                        </div>
                        <div id="sec-ecommerce-chart-line" class="chart-shadow"></div>
                        <div id="sec-ecommerce-chart-bar" ></div>
                    </div>
                </div>
            </div>
            <!-- sale 2 card end -->

            <!-- product and new customar start -->
            <div class="col-xl-4 col-md-6">
                <div class="card new-cust-card">
                    <div class="card-header">
                        <h3>{{ __('New Customers')}}</h3>
                        <div class="card-header-right">
                            <ul class="list-unstyled card-option">
                                <li><i class="ik ik-chevron-left action-toggle"></i></li>
                                <li><i class="ik ik-minus minimize-card"></i></li>
                                <li><i class="ik ik-x close-card"></i></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-block">
                        <div class="align-middle mb-25">
                            <img src="../img/users/1.jpg" alt="user image" class="rounded-circle img-40 align-top mr-15">
                            <div class="d-inline-block">
                                <a href="#!"><h6>{{ __('Alex Thompson')}}</h6></a>
                                <p class="text-muted mb-0">{{ __('Cheers!')}}</p>
                                <span class="status active"></span>
                            </div>
                        </div>
                        <div class="align-middle mb-25">
                            <img src="../img/users/2.jpg" alt="user image" class="rounded-circle img-40 align-top mr-15">
                            <div class="d-inline-block">
                                <a href="#!"><h6>{{ __('John Doue')}}</h6></a>
                                <p class="text-muted mb-0">{{ __('stay hungry stay foolish!')}}</p>
                                <span class="status active"></span>
                            </div>
                        </div>
                        <div class="align-middle mb-25">
                            <img src="../img/users/3.jpg" alt="user image" class="rounded-circle img-40 align-top mr-15">
                            <div class="d-inline-block">
                                <a href="#!"><h6>{{ __('Alex Thompson')}}</h6></a>
                                <p class="text-muted mb-0">{{ __('Cheers!')}}</p>
                                <span class="status deactive text-mute"><i class="far fa-clock mr-10"></i>{{ __('30 min ago')}}</span>
                            </div>
                        </div>
                        <div class="align-middle mb-25">
                            <img src="../img/users/4.jpg" alt="user image" class="rounded-circle img-40 align-top mr-15">
                            <div class="d-inline-block">
                                <a href="#!"><h6>{{ __('John Doue')}}</h6></a>
                                <p class="text-muted mb-0">{{ __('Cheers!')}}</p>
                                <span class="status deactive text-mute"><i class="far fa-clock mr-10"></i>{{ __('10 min ago')}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-8 col-md-6">
                <div class="card table-card">
                    <div class="card-header">
                        <h3>{{ __('New Products')}}</h3>
                        <div class="card-header-right">
                            <ul class="list-unstyled card-option">
                                <li><i class="ik ik-chevron-left action-toggle"></i></li>
                                <li><i class="ik ik-minus minimize-card"></i></li>
                                <li><i class="ik ik-x close-card"></i></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-block">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Product Name')}}</th>
                                        <th>{{ __('Image')}}</th>
                                        <th>{{ __('Status')}}</th>
                                        <th>{{ __('Price')}}</th>
                                        <th>{{ __('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ __('HeadPhone')}}</td>
                                        <td><img src="../img/widget/p1.jpg" alt="" class="img-fluid img-20"></td>
                                        <td>
                                            <div class="p-status bg-green"></div>
                                        </td>
                                        <td>{{ __('$10')}}</td>
                                        <td>
                                            <a href="#!"><i class="ik ik-edit f-16 mr-15 text-green"></i></a>
                                            <a href="#!"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Iphone 6')}}</td>
                                        <td><img src="../img/widget/p2.jpg" alt="" class="img-fluid img-20"></td>
                                        <td>
                                            <div class="p-status bg-green"></div>
                                        </td>
                                        <td>{{ __('$2')}}0</td>
                                        <td><a href="#!"><i class="ik ik-edit f-16 mr-15 text-green"></i></a><a href="#!"><i class="ik ik-trash-2 f-16 text-red"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Jacket')}}</td>
                                        <td><img src="../img/widget/p3.jpg" alt="" class="img-fluid img-20"></td>
                                        <td>
                                            <div class="p-status bg-green"></div>
                                        </td>
                                        <td>{{ __('$35')}}</td>
                                        <td><a href="#!"><i class="ik ik-edit f-16 mr-15 text-green"></i></a><a href="#!"><i class="ik ik-trash-2 f-16 text-red"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Sofa')}}</td>
                                        <td><img src="../img/widget/p4.jpg" alt="" class="img-fluid img-20"></td>
                                        <td>
                                            <div class="p-status bg-green"></div>
                                        </td>
                                        <td>{{ __('$85')}}</td>
                                        <td><a href="#!"><i class="ik ik-edit f-16 mr-15 text-green"></i></a><a href="#!"><i class="ik ik-trash-2 f-16 text-red"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Iphone 6')}}</td>
                                        <td><img src="../img/widget/p2.jpg" alt="" class="img-fluid img-20"></td>
                                        <td>
                                            <div class="p-status bg-green"></div>
                                        </td>
                                        <td>{{ __('$20')}}</td>
                                        <td><a href="#!"><i class="ik ik-edit f-16 mr-15 text-green"></i></a><a href="#!"><i class="ik ik-trash-2 f-16 text-red"></i></a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            <!-- product and new customar end -->
            <!-- Application Sales start -->
            <div class="col-md-12">
                <div class="card table-card">
                    <div class="card-header">
                        <h3>{{ __('Application Sales')}}</h3>
                        <div class="card-header-right">
                            <ul class="list-unstyled card-option">
                                <li><i class="ik ik-chevron-left action-toggle"></i></li>
                                <li><i class="ik ik-minus minimize-card"></i></li>
                                <li><i class="ik ik-x close-card"></i></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-block p-b-0">
                        <div class="table-responsive scroll-widget">
                            <table class="table table-hover table-borderless mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Application')}}</th>
                                        <th>{{ __('Sales')}}</th>
                                        <th>{{ __('Change')}}</th>
                                        <th>{{ __('Avg Price')}}</th>
                                        <th>{{ __('Total')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-inline-block align-middle">
                                                <h6>{{ __('Able Pro')}}</h6>
                                                <p class="text-muted mb-0">{{ __('Powerful Admin Theme')}}</p>
                                            </div>
                                        </td>
                                        <td>{{ __('16,300')}}</td>
                                        <td>
                                            <div id="app-sale1"></div>
                                        </td>
                                        <td>$53</td>
                                        <td class="text-blue">{{ __('$15,652')}}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-inline-block align-middle">
                                                <h6>{{ __('Photoshop')}}</h6>
                                                <p class="text-muted mb-0">{{ __('Design Software')}}</p>
                                            </div>
                                        </td>
                                        <td>{{ __('26,421')}}</td>
                                        <td>
                                            <div id="app-sale2"></div>
                                        </td>
                                        <td>{{ __('$35')}}</td>
                                        <td class="text-blue">{{ __('$18,785')}}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-inline-block align-middle">
                                                <h6>{{ __('Guruable')}}</h6>
                                                <p class="text-muted mb-0">{{ __('Best Admin Template')}}</p>
                                            </div>
                                        </td>
                                        <td>{{ __('8,265')}}</td>
                                        <td>
                                            <div id="app-sale3"></div>
                                        </td>
                                        <td>{{ __('$98')}}</td>
                                        <td class="text-blue">{{ __('$9,652')}}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-inline-block align-middle">
                                                <h6>{{ __('Flatable')}}</h6>
                                                <p class="text-muted mb-0">{{ __('Admin App')}}</p>
                                            </div>
                                        </td>
                                        <td>{{ __('10,652')}}</td>
                                        <td>
                                            <div id="app-sale4"></div>
                                        </td>
                                        <td>{{ __('$20')}}</td>
                                        <td class="text-blue">{{ __('$7,856')}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="text-right">
                            <a href="#!" class=" b-b-primary text-primary">{{ __('View all Projects')}}</a>
                        </div>
                    </div>
                </div>
            </div>--}}
            <!-- Application Sales end -->
    	</div>
    </div>
	<!-- push external js -->
    @push('script')
        <script src="{{ asset('plugins/owl.carousel/dist/owl.carousel.min.js') }}"></script>
        <script src="{{ asset('plugins/chartist/dist/chartist.min.js') }}"></script>
        <script src="{{ asset('plugins/flot-charts/jquery.flot.js') }}"></script>
        <!-- <script src="{{ asset('plugins/flot-charts/jquery.flot.categories.js') }}"></script> -->
        <script src="{{ asset('plugins/flot-charts/curvedLines.js') }}"></script>
        <script src="{{ asset('plugins/flot-charts/jquery.flot.tooltip.min.js') }}"></script>

        <script src="{{ asset('plugins/amcharts/amcharts.js') }}"></script>
        <script src="{{ asset('plugins/amcharts/serial.js') }}"></script>
        <script src="{{ asset('plugins/amcharts/themes/light.js') }}"></script>
       
        
        <script src="{{ asset('js/widget-statistic.js') }}"></script>
        <script src="{{ asset('js/widget-data.js') }}"></script>
        <script src="{{ asset('js/dashboard-charts.js') }}"></script>
        
    @endpush
@endsection