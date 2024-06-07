@yield('css')
<style>
.dataTables_wrapper .dataTables_processing {
    position: fixed !important;
    top: 80% !important;
    background:#F8F8FB;
}
.pac-container {
    z-index: 10000 !important;
}
</style>
<style>
    .newmodal {
        display: none;
        position: relative;
        left: 46%;
        transform: translate(45%, -227%);
        width: 400px;
        padding: 20px;
        margin: 10px;
        background-color: #fff;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        z-index: 1;
        text-align: center;
        border-radius: 20px
    }
</style>
<!-- Bootstrap Css -->
<link href="{{ URL::asset('/assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
<!-- Icons Css -->
<link href="{{ URL::asset('/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
<!-- App Css-->
<link href="{{ URL::asset('/assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
<link
      href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
      rel="stylesheet"
/>
