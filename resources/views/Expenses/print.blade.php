
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <link rel="stylesheet" href=" {{asset('assets/css/bootstrap.min.css')}}">

    <link rel="stylesheet" href="{{asset('assets/css/printA5.css')}}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <style>
        *{
            padding: 0 !important;
        }
        .row{
            width: 95% !important;
            margin: auto !important;
        }
        th{
            font-size: 11px;
            font-weight: bold;
        }
        td{
            font-size: 10px;
            font-weight: normal;
        }
    </style>

</head>
<body>
    <header style="width: 100% ; display: block; margin: auto ; " >

        <div class="row" style="direction: ltr;">
            <div class="col-4 c">
                <span style="text-align: left; font-size:15px;">{{$company ? $company -> name_en : ''}}

                    <br> C.R :   {{$company ? $company -> registrationNumber : ''}}
                <br>  Vat No :   {{$company ? $company -> taxNumber : ''}}
                <br>  Tel :   {{ $company ? $company -> phone : ''}}

             </span>
            </div>
            <div class="col-4 c">
                <label style="text-align: center; font-weight: bold"> مستند قبض نقدية  </label>
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

<div class="container"  style="width: 100%  !important; display: block; margin: 0 auto ; padding: 0">
    <div class="row" style="width: 100%; direction:rtl;">
        <div class="col-6">
            <table  style="width: 100%;  direction: rtl" class="table-bordered">
                <thead>
                <tr>
                    <th class="text-center" style="padding: 5px">تاريخ المستند</th>
                    <th class="text-center" style="padding: 5px">{{\Carbon\Carbon::parse($bill -> date) -> format('d- m -Y') }}</th>
                </tr>
                <tr>
                    <th class="text-center" style="padding: 5px">رقم المستند</th>
                    <th class="text-center"  style="padding: 5px">{{$bill -> docNumber}}</th>


                </tr>
                </thead>
            </table>
        </div>

        <div class="col-6">
            <table style="width: 100% ; direction: rtl" class="table-bordered">
                <thead>
                <tr>
                    <th class="text-center" style="padding: 5px"> قيمة المستند  </th>
                    <th class="text-center"  style="padding: 5px">{{$bill -> amount }}</th>
                </tr>
                <tr>
                    <th class="text-center" style="padding: 5px"> نوع الدفع</th>
                    <th class="text-center" style="padding: 5px"> {{ $bill -> payment_type == 0 ? 'كاش' : 'شبكة' }} </th>
                </tr>
                </thead>
            </table>
        </div>

    </div>
            <h6 class="text-center" style="display: block; margin:20px auto ; font-weight: bold; width: 90%">مستند صرف   </h6>


            <div class="row" style="width: 100%; margin: 10px auto !important; direction:rtl;">
                <div class="col-2">
                    <label>  اصرفوا للمكرم </label>
                </div>
                <div class="col-10 text-center">
                    <label>{{ $bill -> client }}</label>
                </div>

                <div class="col-2">
                    <label> مبلغ وقدره  </label>
                </div>
                <div class="col-10 text-center">
                    <label>{{ $bill -> amount  }} ---  {{ $valAr }}</label>
                </div>

                @if($bill -> payment_type == 0)
                <div class="col-2">
                    <label>  نقدا   </label>
                </div>
                <div class="col-2">
                    <label>بتاريخ</label>
                </div>
                <div class="col-8 text-center">
                    <label>{{ $bill -> date }}</label>
                </div>
                @else
                <div class="col-2">
                    <label>  شيك رقم   </label>
                </div>
                <div class="col-2 text-right ">
                    <label>..............</label>
                </div>
                <div class="col-2">
                    <label>   علي بنك   </label>
                </div>
                <div class="col-2 text-right ">
                    <label>..............</label>
                </div>
                <div class="col-2">
                    <label>بتاريخ</label>
                </div>
                <div class="col-2">
                    <label>{{ $bill -> date }}</label>
                </div>
                @endif
               <div class="col-2">
                    <label>  و ذلك مقابل    </label>
                </div>

                <div class="col-8">
                    <label>..............................................................................</label>
                </div>




            </div>
    <div class="row " style="direction:rtl ; justify-content: center ;">
        <div class="col-4 text-center">
            <span>  المستلم</span> <br>
            <span>............</span>
        </div>
        <div class="col-4 text-center">
            <span>   المحاسب</span> <br>
            <span>............</span>
        </div>
        <div class="col-4 text-center">
            <span>   مدير الفرع</span> <br>
            <span>............</span>
        </div>
    </div>



</div>



<script>


    $(document).ready(function () {
        printPage();

    });

    function printPage(){


        window.print();
    }

</script>
</body>
</html>
