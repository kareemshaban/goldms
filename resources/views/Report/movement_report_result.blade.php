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
    @include('layouts.side' , ['slag' => 14 , 'subSlag' => 1445])    <!-- End of Sidebar -->

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
                <div class="d-sm-flex align-items-center justify-content-between mb-4 no-print" style="padding: 8px">
                    <h1 class="h3 mb-0 text-primary-800 no-print">{{__('main.reports')}} / {{__('main.movement_report')}}</h1>
                    <button type="button" class="btn btn-info no-print" id="btnPrint">Print</button>
                </div>

                <div class="card-body px-0 pt-0 pb-2">
                    <div class="card-header py-3 " style="border:solid 1px gray">
                        <header>
                            <div class="container">
                                <div class="row" style="direction: ltr;">
                                    <div class="col-sm c">
                                        <span style="text-align: left; font-size:15px;">{{$company ? $company -> name_en : ''}}

                                    <br> C.R :   {{$company ? $company -> registrationNumber : ''}}
                                   <br>  Vat No :   {{$company ? $company -> taxNumber : ''}}
                                  <br>  Tel :   {{ $company ? $company -> phone : ''}}

                               </span>
                                    </div>
                                    <div class="col-sm c">
                                        <label style="text-align: center; font-weight: bold"> تقرير حركة فرع</label>
                                    </div>
                                    <div class="col-sm c">
                                   <span style="text-align: right;">{{$company ? $company -> name_ar : ''}}

                                    <br>  س.ت : {{$company ? $company -> taxNumber : ''}}
                                   <br>  ر.ض :  {{$company ? $company -> registrationNumber : ''}}
                                  <br>  تليفون :   {{$company ? $company -> phone : ''}}
                                   </span>
                                    </div>
                                </div>
                            </div>

                        </header>

                    </div>
                    <div class="card shadow mb-4 ">

                        <div class="card-body">
                            <h4 class="text-center">  {{Config::get('app.locale') == 'ar' ? $period_ar : $period}} </h4>

                            <div class="table-responsive">

                                <?php $totalGardWeight = 0 ;  ?>
                                <table class="table table-bordered"  width="100%" cellspacing="0">
                                    <thead>
                                    <tr>
                                        <th class="text-center"> بيان</th>
                                        @foreach ($karats as $karat )
                                        <th class="text-center">{{$karat -> name_ar}}</th>

                                        @endforeach
                                        <th class="text-center">إجمالي الوزن</th>
                                        <th class="text-center">الريال</th>
                                        <th class="text-center">الأجور</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                       <tr>
                                        <td class="text-center">المبيعات</td>
                                        <?php $total1 = 0 ; ?>
                                        @foreach ($karats as $karat )
                                        <?php ${"w" . $karat -> id}   = 0 ;   ?>
                                        @if( isset($data[$karat -> id]) )
                                        <?php $total1 += $data[$karat -> id]['out_weight'] ; ?>
                                        <?php ${"w" . $karat -> id}  = $data[$karat -> id]['out_weight'] ;   ?>
                                        <td class="text-center">{{  $data[$karat -> id]['out_weight']}}</td>
                                       @else
                                       <?php ${"w" . $karat -> id}  =  0;   ?>
                                       <td class="text-center"> 0 </td>

                                       @endif

                                        @endforeach
                                        <td class="text-center">{{ $total1 }}</td>
                                        <td class="text-center">{{ $salesW + $salesO + $returnW + $returnO }}</td>
                                        <td class="text-center">{{  $salesWorkVAl -> total  +   $returnWorkVAl -> total}}</td>
                                       </tr>
                                       <tr>
                                        <td class="text-center">مردود المبيعات</td>
                                        <?php $total2 = 0 ; ?>
                                        @foreach ($karats as $karat )
                                        @if( isset($reW[$karat -> id]) && isset($reO[$karat -> id]) )

                                        <?php ${"w" . $karat -> id}  -=  $reW[$karat -> id]['weight']  + $reO[$karat -> id]['weight'] ;   ?>
                                        <?php $total2 += $reW[$karat -> id]['weight']  + $reO[$karat -> id]['weight']; ?>

                                        <td class="text-center">{{  $reW[$karat -> id]['weight']  + $reO[$karat -> id]['weight']  }}</td>
                                       @elseif(isset($reW[$karat -> id]) && !isset($reO[$karat -> id]))
                                       <?php $total2 += $reW[$karat -> id]['weight'] ; ?>
                                       <?php ${"w" . $karat -> id}  -=  $reW[$karat -> id]['weight'] ;   ?>
                                       <td class="text-center">{{  $reW[$karat -> id]['weight']   }}</td>

                                       @elseif(!isset($reW[$karat -> id]) && isset($reO[$karat -> id]))
                                       <?php $total2 += $reO[$karat -> id]['weight'] ; ?>
                                       <?php ${"w" . $karat -> id}  -=  $reO[$karat -> id]['weight'] ;   ?>

                                       <td class="text-center">{{  $reO[$karat -> id]['weight']  }}</td>
                                       @else
                                       <td class="text-center">0</td>
                                       @endif

                                       @endforeach
                                        <td class="text-center">{{ $total2 }}</td>
                                        <td class="text-center">{{$returnW + $returnO }}</td>
                                        <td class="text-center">{{ $returnWorkVAl -> total }}</td>
                                       </tr>
                                       <tr>
                                        <td class="text-center">المبيعات الفعلية</td>
                                        <?php $total3 = 0 ; ?>

                                        @foreach ($karats as $karat )
                                        <?php $total3 += ${"w" . $karat -> id}   ; ?>

                                        <?php $totalGardWeight += ${"w" . $karat -> id}  * $karat -> transform_factor ;  ?>

                                        <td class="text-center">{{ ${"w" . $karat -> id}   }}</td>



                                        @endforeach
                                        <td class="text-center">{{ $total3 }}</td>
                                        <td class="text-center">{{ $salesW + $salesO  }}</td>
                                        <td class="text-center">{{ $salesWorkVAl -> total}}</td>

                                       </tr>
                                       <tr>
                                        <td class="text-center">المشتريات</td>
                                        <?php $total3 = 0 ; ?>

                                        @foreach ($karats as $karat )
                                        @if( isset($data[$karat -> id]) )
                                        <?php $total3 += $data[$karat -> id]['enter_weight'] ; ?>

                                        <td class="text-center">{{  $data[$karat -> id]['enter_weight']}}</td>
                                       @else
                                       <td class="text-center"> 0 </td>

                                       @endif
                                    @endforeach
                                        <td class="text-center">{{ $total3 }}</td>
                                        <td class="text-center">{{ $purchaseW + $purchaseO  }}</td>
                                        <td class="text-center">0</td>
                                       </tr>

                                    </tbody>

                                </table>

                                <h2 class="text-right">المصروفات</h2>
                                <table class="table table-bordered"  width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="text-center"> الحساب</th>
                                            <th class="text-center">إجمالي المصروفات</th>
                                            <th class="text-center"> تحمل الجرام</th>

                                        </tr>
                                        <?php $total = 0 ;  ?>
                                        <?php  $small = 0 ;  ?>
                                        @foreach ($exp as $account => $bill )
                                        <tr>
                                         <td class="text-center">{{ $account  }}</td>
                                         <td class="text-center">{{ $bill['total'] }}   </td>
                                         <td class="text-center">{{ number_format($bill['total']  / $totalGardWeight , 2)  }}  </td>
                                        </tr>

                                        <?php  $total +=  $bill['total'] ; ?>
                                        <?php     $small += $bill['total']  / $totalGardWeight  ; ?>
                                        @endforeach
                                        <tr>
                                            <td class="text-center"> الإجماليات</td>
                                         <td class="text-center">{{ number_format( $total , 2)  }}   </td>
                                         <td class="text-center">{{ number_format($small , 2)  }}  </td>
                                        </tr>




                                    </thead>


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

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>


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



<!-- Page level custom scripts -->

<script type="text/javascript">
    let id = 0;


    $(document).ready(function () {
        $(document).on('click', '#btnPrint', function (event) {
            printPage();

        });

    });
    function printPage(){
        var css = '@page { size: landscape; }',
            head = document.head || document.getElementsByTagName('head')[0],
            style = document.createElement('style');

        style.type = 'text/css';
        style.media = 'print';

        if (style.styleSheet){
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }

        head.appendChild(style);

        window.print();
    }
</script>

</body>

</html>



