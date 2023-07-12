
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Gold MS</title>

    <!-- Custom fonts for this template-->
    <link href="{{asset('assets/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="{{asset('assets/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="{{asset('assets/css/sb-admin-2.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/printA4Landscape.css')}}" rel="stylesheet">

</head>

<body id="page-top" @if(Config::get('app.locale') == 'ar') style="direction: rtl" @endif>

<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    @include('layouts.side' , ['slag' => 14 , 'subSlag' => 1446])
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            @include('layouts.header')
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <div class="container-fluid">
                @include('flash-message')
                <div class="d-sm-flex align-items-center justify-content-between mb-4" style="padding: 8px">
                    <h1 class="h3 mb-0 text-primary-800 no-print">{{__('main.accounting')}} / {{__('main.account_movement_report')}}</h1>
                    <button type="button" class="btn btn-info no-print" id="btnPrint">Print</button>
                </div>

                <div class="card-body px-0 pt-0 pb-2">

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 " style="border:solid 1px gray">
                            <header>
                                    <div class="row" style="direction: ltr;">
                                        <div class="col-4 c">
                                            <span style="text-align: left; font-size:15px;">{{$company ? $company -> name_en : ''}}

                                        <br> C.R :   {{$company ? $company -> registrationNumber : ''}}
                                       <br>  Vat No :   {{$company ? $company -> taxNumber : ''}}
                                      <br>  Tel :   {{ $company ? $company -> phone : ''}}

                                   </span>
                                        </div>
                                        <div class="col-4 c">
                                            <label style="text-align: center; font-weight: bold"> تقرير حركة حساب</label>
                                        </div>
                                        <div class="col-4 c">
                                       <span style="text-align: right;">{{$company ? $company -> name_ar : ''}}

                                        <br>  س.ت : {{$company ? $company -> taxNumber : ''}}
                                       <br>  ر.ض :  {{$company ? $company -> registrationNumber : ''}}
                                      <br>  تليفون :   {{$company ? $company -> phone : ''}}
                                       </span>
                                        </div>
                                    </div>

                            </header>

                        </div>


                        <div class="card-body">
                            <h3 class="text-center">  {{Config::get('app.locale') == 'ar' ? $period_ar : $period}} </h3>

                            <div class="table-responsive p-0">
                                <table  class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7 ps-2" rowspan="2">{{__('main.account_name')}}</th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7" rowspan="1" colspan="2">{{__('main.Before_Credit')}}</th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7" rowspan="1" colspan="2">{{__('main.movement')}}</th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7" rowspan="1" colspan="2"> {{__('main.After_Credit')}}</th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7" rowspan="1" colspan="2"> {{__('main.balance')}}</th>

                                    </tr>
                                    <tr>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7" rowspan="1" >{{__('main.Credit')}}</th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7" rowspan="1" >{{__('main.Debit')}}</th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7" rowspan="1" >{{__('main.Credit')}}</th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7" rowspan="1" >{{__('main.Debit')}}</th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7" rowspan="1" >{{__('main.Credit')}}</th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7" rowspan="1" >{{__('main.Debit')}}</th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7" rowspan="1" >{{__('main.Credit')}}</th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7" rowspan="1" >{{__('main.Debit')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php  $before_credit = 0 ;
                                    $before_debit = 0 ;
                                    $credit = 0 ;
                                    $debit = 0 ;
                                    $total_credit = 0 ;
                                    $total_debit = 0 ;
                                    $balance_credit = 0 ;
                                    $balance_debit = 0 ;
                                    ?>

                                    @foreach($accounts as $index=>$unit)
                                        <tr>
                                            <td class="text-center">{{$unit->name}}</td>
                                            <td class="text-center">{{$unit->before_credit}}</td>
                                            <td class="text-center">{{$unit->before_debit}}</td>
                                            <td class="text-center">{{$unit->credit}}</td>
                                            <td class="text-center">{{$unit->debit}}</td>
                                            <td class="text-center">{{$unit->before_credit + $unit->credit}}</td>
                                            <td class="text-center">{{$unit->before_debit + $unit->debit}}</td>

                                            <td class="text-center">{{ (($unit->before_credit + $unit->credit)  - ($unit->before_debit + $unit->debit))  > 0 ? (($unit->before_credit + $unit->credit)  - ($unit->before_debit + $unit->debit))  : 0 }}</td>
                                            <td class="text-center">{{ (($unit->before_credit + $unit->credit)  - ($unit->before_debit + $unit->debit))  < 0 ? (($unit->before_credit + $unit->credit)  - ($unit->before_debit + $unit->debit)) * - 1  : 0 }}</td>

                                        </tr>


                                        <?php  $before_credit += $unit->before_credit ;
                                        $before_debit += $unit->before_debit ;
                                        $credit += $unit->credit ;
                                        $debit += $unit->debit ;
                                        $total_credit += $unit->before_credit + $unit->credit ;
                                        $total_debit += $unit->before_debit + $unit->debit ;
                                        $balance_credit += (($unit->before_credit + $unit->credit)  - ($unit->before_debit + $unit->debit))  > 0 ? (($unit->before_credit + $unit->credit)  - ($unit->before_debit + $unit->debit))  : 0 ;
                                        $balance_debit += (($unit->before_credit + $unit->credit)  - ($unit->before_debit + $unit->debit))  < 0 ? (($unit->before_credit + $unit->credit)  - ($unit->before_debit + $unit->debit)) * - 1  : 0 ;
                                        ?>
                                    @endforeach

                                    <tr style="background: #c3e6cb">
                                    <td class="text-center"> اجمالي الحساب  </td>
                                        <td class="text-center">{{$before_credit}}  </td>
                                        <td class="text-center">{{$before_debit}}  </td>
                                        <td class="text-center">{{$credit}}  </td>
                                        <td class="text-center">{{$debit}}  </td>
                                        <td class="text-center">{{$total_credit}}  </td>
                                        <td class="text-center">{{$total_debit}}  </td>
                                        <td class="text-center">{{$balance_credit}}  </td>
                                        <td class="text-center">{{$balance_debit}}  </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                </div>


            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->

        <!-- Footer -->
        @include('layouts.footer')
        <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>




<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('click', '#btnPrint', function (event) {
            window.print();

        });

    });


</script>




<script src="{{asset('assets/vendor/jquery/jquery.min.js')}}"></script>
<script src="{{asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

<!-- Core plugin JavaScript-->
<script src="{{asset('assets/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

<!-- Custom scripts for all pages-->
<script src="{{asset('assets/js/sb-admin-2.min.js')}}"></script>


<script src="{{asset('assets/vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>

<!-- Page level custom scripts -->
<script src="{{asset('assets/js/demo/datatables-demo.js')}}"></script>

</body>

</html>

