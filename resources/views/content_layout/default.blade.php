<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Unforgettable | Dashboard</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  {!! HTML::style('bootstrap/css/bootstrap.min.css') !!}
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <script src="{{ asset('js/swi204cs.js') }}" ></script>x
  
  <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
  {{-- {!! HTML::style('plugins/select2/select2.min.css') !!} --}}
  {!! HTML::style('plugins/datatables/dataTables.bootstrap.css') !!}
  {!! HTML::style('plugins/select2/select2.min.css') !!}
  {!! HTML::style('dist/css/AdminLTE.min.css') !!}
  <!-- AdminLTE Skins. Choose a skin from the css/skins
    folder instead of downloading all of them to reduce the load. -->
    {!! HTML::style('dist/css/skins/_all-skins.min.css') !!}
    
    <!-- iCheck -->
    {!! HTML::style('plugins/iCheck/flat/blue.css') !!}
    <!-- Morris chart -->
    {!! HTML::style('plugins/morris/morris.css') !!}
    <!-- jvectormap -->
    {!! HTML::style('plugins/jvectormap/jquery-jvectormap-1.2.2.css') !!}
    
    <!-- Date Picker -->
    {!! HTML::style('plugins/datepicker/datepicker3.css') !!}
    {!! HTML::style('plugins/daterangepicker/daterangepicker.css') !!}
    <!-- Daterange picker -->
    {{-- {!! HTML::style('plugins/daterangepicker/daterangepicker.css') !!} --}}
    <!-- bootstrap wysihtml5 - text editor -->
    {{-- {!! HTML::style('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') !!} --}}
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->
      <style type="text/css">
     tr.tr_border_bottom {
   border-bottom: 2px solid black;
   } 
thead.border_thead {
    border: 3px solid black;
}
  #divLoading
  {
  display : none;
  }
  #divLoading.show
  {
  display : block;
  position : fixed;
  z-index: 100;
  background-image : url('img/3.gif');
  background-color:#666;
  opacity : 0.4;
  background-repeat : no-repeat;
  background-position : center;
  left : 0;
  bottom : 0;
  right : 0;
  top : 0;
  }
  .zindex{
    z-index: 10;
  }
  #loadinggif.show
  {
  left : 50%;
  top : 50%;
  position : absolute;
  z-index : 101;
  width : 32px;
  height : 32px;
  margin-left : -16px;
  margin-top : -16px;
  }
  #divLoading
  {
  display : none;
  }
  #divLoading.show
  {
  display : block;
  position : fixed;
  z-index: 100;
  background-image : url('{{asset('img/loading_gif.gif')}}');
  background-color:#666;
  opacity : 0.4;
  background-repeat : no-repeat;
  background-position : center;
  left : 0;
  bottom : 0;
  right : 0;
  top : 0;
  }
  .zindex{
    z-index: 10;
  }
  .text-danger{
    color: red;
  }
  .badge-success{
    background-color: green;
  }
  .text-light{
    color: white;
  }
  .bg-danger{
    background-color: red;
  }
  </style>
</head>

@php
  $route = \Route::currentRouteName()
@endphp

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="#" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>U</b>TC</span>
      <!-- logo for regular state and mobile devices -->
      <!-- <span class="logo-lg"><b>Admin</b></span> -->
      
      <span class="logo-lg"><b><img src="{{URL::asset('img/logo.png')}}" style="height:50px;width:200px"></b></span>
      

    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- Messages: style can be found in dropdown.less-->
          <li class="dropdown messages-menu">
            <!-- <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-envelope-o"></i>
            </a> -->
            <!-- <ul class="dropdown-menu">
              <li class="header">You have 4 messages</li>
              <li>
                inner menu: contains the actual data
                <ul class="menu">
                  <li>start message
                    <a href="#">
                      <div class="pull-left">
                        <img src="{{ url('dist/img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
                      </div>
                      <h4>
                        Support Team
                        <small><i class="fa fa-clock-o"></i> 5 mins</small>
                      </h4>
                      <p>Why not buy a new awesome theme?</p>
                    </a>
                  </li>
                  end message
                  <li>
                    <a href="#">
                      <div class="pull-left">
                        <img src="{{ url('dist/img/user3-128x128.jpg') }}" class="img-circle" alt="User Image">
                      </div>
                      <h4>
                        AdminLTE Design Team
                        <small><i class="fa fa-clock-o"></i> 2 hours</small>
                      </h4>
                      <p>Why not buy a new awesome theme?</p>
                    </a>
                  </li>
                  <li>
                    <a href="#">
                      <div class="pull-left">
                        <img src="{{ url('dist/img/user4-128x128.jpg') }}" class="img-circle" alt="User Image">
                      </div>
                      <h4>
                        Developers
                        <small><i class="fa fa-clock-o"></i> Today</small>
                      </h4>
                      <p>Why not buy a new awesome theme?</p>
                    </a>
                  </li>
                  <li>
                    <a href="#">
                      <div class="pull-left">
                        <img src="{{ url('dist/img/user3-128x128.jpg') }}" class="img-circle" alt="User Image">
                      </div>
                      <h4>
                        Sales Department
                        <small><i class="fa fa-clock-o"></i> Yesterday</small>
                      </h4>
                      <p>Why not buy a new awesome theme?</p>
                    </a>
                  </li>
                  <li>
                    <a href="#">
                      <div class="pull-left">
                        <img src="{{ url('dist/img/user4-128x128.jpg') }}" class="img-circle" alt="User Image">
                      </div>
                      <h4>
                        Reviewers
                        <small><i class="fa fa-clock-o"></i> 2 days</small>
                      </h4>
                      <p>Why not buy a new awesome theme?</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="footer"><a href="#">See All Messages</a></li>
            </ul> -->
          </li>
          <!-- Notifications: style can be found in dropdown.less -->
          <li class="dropdown notifications-menu">
            <!-- <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
            </a> -->
            <!-- <ul class="dropdown-menu">
              <li class="header">You have 10 notifications</li>
              <li>
                inner menu: contains the actual data
                <ul class="menu">
                  <li>
                    <a href="#">
                      <i class="fa fa-users text-aqua"></i> 5 new members joined today
                    </a>
                  </li>
                  <li>
                    <a href="#">
                      <i class="fa fa-warning text-yellow"></i> Very long description here that may not fit into the
                      page and may cause design problems
                    </a>
                  </li>
                  <li>
                    <a href="#">
                      <i class="fa fa-users text-red"></i> 5 new members joined
                    </a>
                  </li>
                  <li>
                    <a href="#">
                      <i class="fa fa-shopping-cart text-green"></i> 25 sales made
                    </a>
                  </li>
                  <li>
                    <a href="#">
                      <i class="fa fa-user text-red"></i> You changed your username
                    </a>
                  </li>
                </ul>
              </li>
              <li class="footer"><a href="#">View all</a></li>
            </ul> -->
          </li>
          <!-- Tasks: style can be found in dropdown.less -->
          <li class="dropdown tasks-menu">
            <!-- <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-flag-o"></i>
            </a> -->
            <!-- <ul class="dropdown-menu">
              <li class="header">You have 9 tasks</li>
              <li>
                inner menu: contains the actual data
                <ul class="menu">
                  <li>Task item
                    <a href="#">
                      <h3>
                        Design some buttons
                        <small class="pull-right">20%</small>
                      </h3>
                      <div class="progress xs">
                        <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                          <span class="sr-only">20% Complete</span>
                        </div>
                      </div>
                    </a>
                  </li>
                  end task item
                  <li>Task item
                    <a href="#">
                      <h3>
                        Create a nice theme
                        <small class="pull-right">40%</small>
                      </h3>
                      <div class="progress xs">
                        <div class="progress-bar progress-bar-green" style="width: 40%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                          <span class="sr-only">40% Complete</span>
                        </div>
                      </div>
                    </a>
                  </li>
                  end task item
                  <li>Task item
                    <a href="#">
                      <h3>
                        Some task I need to do
                        <small class="pull-right">60%</small>
                      </h3>
                      <div class="progress xs">
                        <div class="progress-bar progress-bar-red" style="width: 60%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                          <span class="sr-only">60% Complete</span>
                        </div>
                      </div>
                    </a>
                  </li>
                  end task item
                  <li>Task item
                    <a href="#">
                      <h3>
                        Make beautiful transitions
                        <small class="pull-right">80%</small>
                      </h3>
                      <div class="progress xs">
                        <div class="progress-bar progress-bar-yellow" style="width: 80%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                          <span class="sr-only">80% Complete</span>
                        </div>
                      </div>
                    </a>
                  </li>
                  end task item
                </ul>
              </li>
              <li class="footer">
                <a href="#">View all tasks</a>
              </li>
            </ul> -->
          </li>
     
          <!-- code for modal -->
          
          <!-- end code here-->

          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <!-- <img src="{{ url('dist/img/user2-160x160.jpg') }}" class="user-image" alt="User Image"> -->
              <span class="hidden-xs"> {{ Auth::user('name')->name }} </span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <!-- <img src="{{ url('dist/img/user2-160x160.jpg') }}" class="img-circle" alt="User Image"> -->
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="#" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <!-- <a href="#" class="btn btn-default btn-flat">Sign out</a> -->
                  {!! HTML::link('logout','Sign out',array('class'=>'btn btn-default btn-flat')) !!} 
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
         <!--  <li>
           <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
         </li> -->
        </ul>
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <!-- <img src="{{ url('dist/img/user2-160x160.jpg') }}" class="img-circle" alt="User Image"> -->
        </div>
        <div class="pull-left info">
          <!-- <p>Alexander Pierce</p> -->
          <!-- <a href="#"><i class="fa fa-circle text-success"></i> Online</a> -->
        </div>
      </div>
      <!-- search form -->
      <!-- <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form> -->
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        <li class="header">MAIN NAVIGATION</li>
        <li class="active treeview">
          <a href="{{ URL::to('/')}}">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
            <span class="pull-right-container">
              <i class=""></i>
            </span>
          </a>
        </li>
       <!--  <li class="treeview">
         <a href="#">
           <i class="fa fa-files-o"></i> <span>Fatwa</span>
           <span class="pull-right-container">
             <i class="fa fa-angle-left pull-right"></i>
           </span>
         </a>
         <ul class="treeview-menu">
           <li><a href="{{ URL::to('add-fatwa')}}"><i class="fa fa-plus"></i> Add Fatwa</a></li>
           <li class=""><a href="{{ URL::to('view-fatwa')}}"><i class="fa fa-eye"></i> View Fatwas</a></li>
         </ul>
       </li> -->
       <!--  <li class="treeview">
         <a href="#">
           <i class="fa fa-files-o"></i> <span>Fatwa category</span>
           <span class="pull-right-container">
             <i class="fa fa-angle-left pull-right"></i>
           </span>
         </a>
         <ul class="treeview-menu">
           <li><a href="{{ URL::to('creat-category')}}"><i class="fa fa-plus"></i> Create Category</a></li>
           <li><a href="{{ URL::to('view-category')}}"><i class="fa fa-eye"></i> View Category</a></li>
         </ul>
       </li> -->
        <!-- <li class="treeview">
          <a href="#">
            <i class="fa fa-files-o"></i> <span>Mujib</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ URL::to('creat-replier')}}"><i class="fa fa-plus"></i> Create Mujib</a></li>
            <li class=""><a href="{{ URL::to('view-replier')}}"><i class="fa fa-eye"></i> View Mujib</a></li>
            
          </ul>
        </li> -->
        <!-- <li class="treeview">
          <a href="#">
            <i class="fa fa-files-o"></i> <span>Book</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ URL::to('creat-book')}}"><i class="fa fa-plus"></i> Create Book</a></li>
            <li class=""><a href="{{ URL::to('view-book')}}"><i class="fa fa-eye"></i> View Book</a></li>
          </ul>
        </li> -->
        <!-- <li class="treeview">
          <a href="#">
            <i class="fa fa-files-o"></i> <span>Chapter</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ URL::to('creat-chapter')}}"><i class="fa fa-plus"></i> Create Chapter</a></li>
            <li class=""><a href="{{ URL::to('view-chapter')}}"><i class="fa fa-eye"></i> View Chapter</a></li>
          </ul>
        </li> -->
        <!-- <li class="treeview">
          <a href="#">
            <i class="fa fa-files-o"></i> <span>Mufti</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ URL::to('creat-mufti')}}"><i class="fa fa-plus"></i> Create Mufti</a></li>
            <li class=""><a href="{{ URL::to('view-mufti')}}"><i class="fa fa-eye"></i> View Mufti</a></li>
          </ul>
        </li> -->
        <?php
        $book_id = @$book_id;
        $id      = @$id;
        ?>
        <li class="treeview {{ $route == 'create-booking' || $route == 'view-booking' || $route == 'view-booking-season' || $route == 'update-booking' || $route == 'view-quotation-version' || $route == 'view-quotation' || $route == 'view-booking-version' ? 'active' : '' }}">
          <a href="#">
            <i class="fa fa-book"></i> <span>Booking </span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            {{-- <li class="{{Request::is('create-booking') ? 'active' : ''}}"><a href="{{ route('create-booking')}}"><i class="fa fa-plus"></i>Create Booking</a></li> --}}
            <li class="{{Request::is('view-booking-season') ? 'active' : ''}}"><a href="{{ route('view-booking-season')}}"><i class="fa fa-eye"></i>View Booking Season</a></li>

          </ul>
        </li>

        <?php
        $id = @$id;
        ?>
        <li class="treeview {{ $route == 'creat-quote' || $route =='view-quote'  || $route =='edit-quote' || $route == 'view-version' || $route == 'recall-version' || $route == 'view-quote-detail' ? 'active' : ''}}" >
          <a href="#">
            <i class="fa fa-file"></i> <span>Quote Management</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            {{-- <li class="{{ $route == 'creat-code' ? 'active' : ''}}"><a href="{{ URL::to('creat-code')}}"><i class="fa fa-plus"></i>Add Code</a></li>
            <li class="{{ $route =='view-code'  || $route =='edit-code' ? 'active' : ''}}"><a href="{{ URL::to('view-code')}}"><i class="fa fa-eye"></i>View Codes</a></li> --}}

            <li class="{{ $route == 'creat-quote' ? 'active' : ''}}"><a href="{{ URL::to('creat-quote')}}"><i class="fa fa-plus"></i>Add Quotes</a></li>
            <li class="{{ $route == 'view-quote' || $route == 'edit-quote' || $route == 'view-version' || $route == 'recall-version' || $route == 'view-quote-detail' ? 'active' : ''}}"><a href="{{ URL::to('view-quote')}}"><i class="fa fa-eye"></i>View Quotes</a></li>
          </ul>

        </li>
        
        <li class="treeview {{ $route == 'template.index'  || $route == 'template.create' || $route == 'template.edit' || $route == 'template.detail' ? 'active' : ''  }}">
          <a href="#">
            <i class="fa fa-clone  "></i> <span>Template Mangment</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="{{ $route == 'template.create' ? 'active' : ''}}"><a href="{{ route('template.create')}}"><i class="fa fa-plus"></i>Create Template</a></li>
            <li class="{{ $route == 'template.index' || $route == 'template.edit' || $route == 'template.detail' ? 'active' : ''}}"><a href="{{ route('template.index')}}"><i class="fa fa-eye"></i>View Template</a></li>
          </ul>
        </li> 
        
        
        <li class="treeview  @if (Request::is('creat-season') || Request::is('view-season') || Request::is('update-season/'.$id)) active @endif">
          <a href="#">
            <i class="fa fa-cloud"></i> <span>Season Management</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="{{Request::is('creat-season') ? 'active' : ''}}"><a href="{{ URL::to('creat-season')}}"><i class="fa fa-plus"></i>Create Season</a></li>
            <li class="{{Request::is('view-season') ? 'active' : ''}}"><a href="{{ URL::to('view-season')}}"><i class="fa fa-eye"></i>View Season</a></li>
          </ul>
        </li>
        
        
        {{-- /// user managment start  --}}
        {{-- old code 
          <li class="treeview @if (Request::is('creat-user') || Request::is('add-role') || Request::is('view-role') || Request::is('view-user') || Request::is('update-user/'.$id) || $route == 'update-user') active @endif">
          <a href="#">
            <i class="fa fa-user"></i> <span>User Management</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="{{Request::is('creat-user') ? 'active' : ''}}"><a href="{{ URL::to('creat-user')}}"><i class="fa fa-plus"></i>Create User</a></li>
            <li class="{{Request::is('view-user') || $route == 'update-user' ? 'active' : ''}}"><a href="{{ URL::to('view-user')}}"><i class="fa fa-eye"></i>View User</a></li>
            <li class="{{Request::is('add-role') ? 'active' : ''}}"><a href="{{ route('add-role')}}"><i class="fa fa-plus"></i>Add Role</a></li>
            <li class="{{Request::is('view-role') ? 'active' : ''}}"><a href="{{ route('view-role')}}"><i class="fa fa-eye"></i>View Roles</a></li>
          </ul>
        </li>  old code--}}
        
        {{-- New --}}
        {{-- /// user managment start  --}}
        <li class="treeview {{ ($route == 'users.index' || $route == 'users.create' || $route == 'roles.index' || $route == 'roles.create')? 'active': NULL }}">
          <a href="#">
            <i class="fa fa-user"></i> <span>User Management</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="{{($route == 'users.index')? 'active' : ''}}"><a href="{{ route('users.index') }}"><i class="fa fa-eye"></i>View Users</a></li>
            <li class="{{($route == 'users.create')? 'active' : ''}}"><a href="{{ route('users.create') }}"><i class="fa fa-plus"></i>Create Users</a></li>
            <li class="{{($route == 'roles.index') ? 'active' : ''}}"><a href="{{ route('roles.index')}}"><i class="fa fa-eye"></i>View Roles</a></li>
            <li class="{{($route == 'roles.create') ? 'active' : ''}}"><a href="{{ route('roles.create')}}"><i class="fa fa-plus"></i>Add Role</a></li>
          </ul>
        </li>
        {{-- /// user managment end  --}}
        {{-- New end  --}}
        {{-- /// user managment end  --}}
        
        
        
        <li class="treeview {{ $route == 'add-category' || $route == 'view-category' || $route == 'update-category' ||  $route == 'add-product' ||  $route == 'update-product' || $route == 'view-product' ||  $route == 'add-supplier' || $route == 'view-supplier' || $route ==  'update-supplier' || $route == 'details-supplier' ? 'active' : ''  }}">
          <a href="#">
            <i class="fa fa-group"></i> <span>Suppliers</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
          <li class="{{ $route == 'add-category' ? 'active' : ''}}"><a href="{{ route('add-category')}}"><i class="fa fa-plus"></i>Add Category</a></li>
          <li class="{{ $route == 'view-category' || $route == 'update-category' ? 'active' : ''}}"><a href="{{ route('view-category')}}"><i class="fa fa-eye"></i>View Categories</a></li>
          <li class="{{ $route == 'add-product' ? 'active' : ''}}"><a href="{{ route('add-product')}}"><i class="fa fa-plus"></i>Add Product</a></li>
          <li class="{{ $route == 'view-product' || $route == 'update-product'  ? 'active' : ''}}"><a href="{{ route('view-product')}}"><i class="fa fa-eye"></i>View Products</a></li>
          <li class="{{ $route == 'add-supplier' ? 'active' : ''}}"><a href="{{ route('add-supplier')}}"><i class="fa fa-plus"></i>Add Supplier</a></li>
          <li class="{{ $route == 'view-supplier' || $route ==  'update-supplier' || $route == 'details-supplier' ? 'active' : ''}}"><a href="{{ route('view-supplier')}}"><i class="fa fa-eye"></i>View Suppliers</a></li>
          {{-- <li class="{{ $route == 'view-supplier-products' ? 'active' : ''}}"><a href="{{ route('view-supplier-products')}}"><i class="fa fa-eye"></i> Suppliers Products</a></li> --}}
          {{-- <li class="{{ $route == 'view-supplier-categories' ? 'active' : ''}}"><a href="{{ route('view-supplier-categories')}}"><i class="fa fa-eye"></i> Suppliers Categories</a></li> --}}
          </ul>
        </li> 
        
        
        
       

        {{-- @if (Request::is('creat-airline') || Request::is('view-airline') || Request::is('creat-payment') || Request::is('view-payment') || Request::is('creat-booking-method') ||  Request::is('view-booking-method') )  active @endif --}}

        <li class="treeview {{ $route == 'creat-airline' || $route == 'view-airline' || $route == 'update-airline' || $route == 'creat-payment' || $route == 'view-payment' || $route == 'update-payment' || $route == 'creat-booking-method' || $route == 'update-payment'|| $route == 'edit-booking-method' || $route == 'view-booking-method' || $route == 'creat-currency' || $route == 'edit-currency' || $route == 'view-currency' || $route == 'brand.create'
        || $route == 'brand.index' || $route == 'holidaytype.create' || $route == 'holidaytype.index' || $route == 'brand.edit' || $route == 'holidaytype.edit' || $route == 'view-manual-rates' ? 'active' : '' }}">
            <a href="#">
                <i class="fa fa-gear"></i>
                <span>Setting</span>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
                <li class="{{ $route == 'creat-airline' ? 'active' : ''}}"><a href="{{ route('creat-airline')}}"><i class="fa fa-plus"></i>Create Airline</a></li>
                <li class="{{ $route == 'view-airline' || $route == 'update-airline' ? 'active' : ''}}"><a href="{{ route('view-airline')}}"><i class="fa fa-eye"></i>View Airline</a></li>
                <li class="{{ $route == 'creat-payment' ? 'active' : ''}}"><a href="{{ route('creat-payment')}}"><i class="fa fa-plus"></i>Create Payment Method</a></li>
                <li class="{{ $route == 'view-payment' || $route == 'update-payment'  ? 'active' : ''}}"><a href="{{ route('view-payment')}}"><i class="fa fa-eye"></i>View Payment Method</a></li>
                <li class="{{ $route == 'creat-booking-method' ? 'active' : '' }}"><a href="{{ route('creat-booking-method')}}"><i class="fa fa-plus"></i>Booking Methods</a></li>
                <li class="{{ $route == 'view-booking-method' || $route == 'edit-booking-method' ? 'active' : '' }}"><a href="{{ route('view-booking-method')}}"><i class="fa fa-eye"></i> View Booking Methods</a></li>
                
                <li class="{{ $route == 'creat-currency'  ? 'active' : '' }}"><a href="{{ route('creat-currency')}}"><i class="fa fa-plus"></i> Add Currency</a></li>
                <li class="{{ $route == 'view-currency' || $route == 'edit-currency' ? 'active' : '' }}"><a href="{{ route('view-currency')}}"><i class="fa fa-eye"></i> View Currency</a></li>
                <li class="{{ $route == 'brand.create'  ? 'active' : '' }}"><a href="{{ route('brand.create')}}"><i class="fa fa-plus"></i> Add Brand</a></li>
                <li class="{{ $route == 'brand.index' || $route == 'brand.edit' ? 'active' : '' }}"><a href="{{ route('brand.index')}}"><i class="fa fa-eye"></i>View Brand</a></li>
                <li class="{{ $route == 'holidaytype.create'  ? 'active' : '' }}"><a href="{{ route('holidaytype.create')}}"><i class="fa fa-plus"></i> Add Holiday Type</a></li>
                <li class="{{ $route == 'holidaytype.index' || $route == 'holidaytype.edit' ? 'active' : '' }}"><a href="{{ route('holidaytype.index')}}"><i class="fa fa-eye"></i>View Holiday Type</a></li>
                <li class="{{ $route == 'view-manual-rates' ? 'active' : '' }}"><a href="{{ route('view-manual-rates')}}"><i class="fa fa-eye"></i>View Manual Rates</a></li>
              </ul>
        </li>


        <!-- <li class="treeview @if (Request::is('create-supervisor') || Request::is('view-supervisor') || Request::is('update-supervisor/'.$id)) active @endif">
          <a href="#">
            <i class="fa fa-users"></i> <span>Supervisor Management</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="{{Request::is('create-supervisor') ? 'active' : ''}}"><a href="{{ route('create-supervisor')}}"><i class="fa fa-plus"></i>Create Supervisor</a></li>
            <li class="{{Request::is('view-supervisor') ? 'active' : ''}}"><a href="{{ route('view-supervisor')}}"><i class="fa fa-eye"></i>View Supervisor</a></li>
          </ul>
        </li> -->
        
      </ul>
    </section>
  </aside>
  @yield('content')

  <!-- jQuery 2.2.3 -->
  {!! HTML::script('plugins/jQuery/jquery-2.2.3.min.js') !!}
  <!-- Bootstrap 3.3.6 -->
  {!! HTML::script('bootstrap/js/bootstrap.min.js') !!}
  <!-- DataTables -->
  {!! HTML::script('plugins/datatables/jquery.dataTables.min.js') !!}
  {!! HTML::script('plugins/datatables/dataTables.bootstrap.min.js') !!}
  <!-- SlimScroll -->
  {!! HTML::script('plugins/slimScroll/jquery.slimscroll.min.js') !!}
  <!-- FastClick -->
  {!! HTML::script('plugins/fastclick/fastclick.js') !!}
  <!-- AdminLTE App -->
  {!! HTML::script('dist/js/app.min.js') !!}
  <!-- AdminLTE for demo purposes -->
  {!! HTML::script('dist/js/demo.js') !!}
  {!! HTML::script('plugins/select2/select2.full.min.js') !!}

  @yield('scripts')
  
   
   
  </body>
</html>