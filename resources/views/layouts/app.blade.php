<!DOCTYPE html>
<html lang="en">
 
   <head>
      <!-- basic -->
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <!-- mobile metas -->
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="viewport" content="initial-scale=1, maximum-scale=1">
      <!-- site metas -->
      <title>@yield('title', 'Srdreambusiness')</title>
      <meta name="keywords" content="">
      <meta name="description" content="">
      <meta name="author" content="">

    <meta name="csrf-token" content="{{ csrf_token() }}">
      <!-- site icon -->
      <link rel="icon" href="{{ asset('inventory/images/fevicon.png')}}" type="image/png" />
      <!-- bootstrap css -->
      <link rel="stylesheet" href="{{ asset('inventory/css/bootstrap.min.css')}}" />
         
      <!-- site css -->
      <link rel="stylesheet" href="{{ asset('inventory/css/style.css')}}" />
      <!-- responsive css -->
      <link rel="stylesheet" href="{{ asset('inventory/css/responsive.css') }}" />
      <!-- color css -->
       <script src="{{ asset('inventory/js/jquery-3.7.1.min.js') }}"></script>
      <!-- select bootstrap -->
      <link rel="stylesheet" href="{{ asset('inventory/css/bootstrap-select.css') }}" />
      <!-- scrollbar css -->
      <link rel="stylesheet" href="{{ asset('inventory/css/perfect-scrollbar.css') }}" />
      <!-- custom css -->
      <link rel="stylesheet" href="{{ asset('inventory/css/custom.css')}}" />

    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
      <![endif]-->
   </head>
<!--    <style>.sidebar_user_info{
      background-color: #d4d4d485;
   }#sidebar{
        background-color: #ffffffff;}
      #sidebar ul li a,.user_info h6 {

    color: black;

}</style> -->
<!-- <style>
   .fixTableHead { overflow: auto!important; max-height:calc( 96vh - 140px);overflow-x: auto; width: 100%;position: relative;
    } 
  .fixTableHead thead th { position: sticky; top: -1px;border: none!important; }
   table { border-collapse: collapse; width: 100%; } 
  .fixTableHead th {background-color: #57c7d4!important;
   
          color:rgb(255, 255, 255)!important; } 
  .fixTableHead th, .fixTableHead td { padding: 10px 5px!important; font-size: 12px!important;
    }
</style> -->
   <body class="dashboard dashboard_1">
      <div class="full_container">
         <div class="inner_container">
            <!-- Sidebar  -->
            <nav id="sidebar" >
               <div class="sidebar_blog_1">
                  <div class="sidebar-header">
                     <div class="logo_section">

                     </div>
                  </div>
                  <div class="sidebar_user_info">
                     <div class="icon_setting"></div>
                     <div class="user_profle_side">
                        <div class="user_img" style="    width: 59px;
    height: 59px;
    float: left;"><img style="    height: 62px;border:1px solid #57c7d4 ;
    width: 62px;" class="img-responsive" src="{{ asset('inventory/images/logo/logo_icon.png') }}" alt="#" /></div>
                        <div class="user_info">
                           <h6></h6>
                          <span class="online_animation"></span>  <p> {{ Auth::user()->name }}</p>
                        <!--   <p>Manager ID: {{ Auth::user()->manager_id ?? 'None Assigned' }}</p>
                          <p>Session: <small>{{ auth::user()->id }}</small></p> -->
                        </div>
                     </div>
                  </div>
               </div>
               <div class="sidebar_blog_2">
         
               
   <x-sidebar/>

               </div>
            </nav>
            <!-- end sidebar -->
            <!-- right content -->
            <div id="content">
               <!-- topbar -->
               <div class="topbar">
                  <nav class="navbar navbar-expand-lg navbar-light">
                     <div class="full">
                        <button type="button" id="sidebarCollapse" class="sidebar_toggle"><i class="fa fa-bars"></i></button>
                        <div class="logo_section">
                           <a href="index.php"><img class="img-responsive" src="{{ asset('inventory/images/logo/logo_icon.png') }}" alt="#" /></a>
                        </div>
                        <div class="right_topbar">
                           <div class="icon_info">
                            
                              <ul class="user_profile_dd">
                                 <li>
                                    <a class="dropdown-toggle" data-toggle="dropdown">
                                        <img class="img-responsive rounded-circle" src="{{ asset('inventory/images/layout_img/user2.png')}}"
                                     alt="#" /><span class="name_user"></span></a>
                                    <div class="dropdown-menu">
                                       <a class="dropdown-item" href="profile.html">My Profile</a>
                                       <a class="dropdown-item" href="settings.html">Settings</a>
                                       <a class="dropdown-item" href="help.html">Help</a>
                                       <form action="{{route('logout')}}" method="post" >
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">
        <i class="fa fa-sign-out-alt w-6"></i> 
        <span>Logout</span>
    </button>
                                       </form>
                                      
                                    </div>
                                 </li>
                              </ul>
                           </div>
                        </div>
                     </div>
                  </nav>
               </div>
               <!-- end topbar -->
               <!-- dashboard inner -->
               <div class="midde_cont">
                  <div class="container-fluid" style="padding-top:8px;">
   <div class="content">
    @yield('content')
</div>
                  </div>
           
               </div>
               <!-- end dashboard inner -->
            </div>
         </div>
      </div>
   <!-- 
<script>
document.addEventListener("DOMContentLoaded", function() {
    const sidebar = document.getElementById("sidebarMenu");
    const currentUrl = window.location.href.split("#")[0]; // ignore hash

    // Loop through all sidebar links
    sidebar.querySelectorAll("a").forEach(function(link) {
        const linkUrl = link.href.split("#")[0]; // ignore hash

        if(linkUrl === currentUrl) {

        console.log(currentUrl);
            // Highlight this link's <li>
            const li = link.closest("li");
            if(li) {
                li.style.backgroundColor = "";
                li.style.color = "";
            }

            // Expand parent menu if inside a collapsible <ul>
            const parentUl = link.closest("ul.collapse");
            if(parentUl) {
                parentUl.classList.add("show"); // expand
                 console.log(parentUl);
                // Highlight mother <li>
                const motherLi = parentUl.closest("li");
                if(motherLi) {
                    motherLi.style.backgroundColor = "";
                    motherLi.style.color = "";
                }
            }
        }
    });
});
</script> -->
      <!-- jQuery -->
       
  
     <!--  <script >
         
document.addEventListener("DOMContentLoaded", function () {

    const submitBtn = document.getElementById("submitBtn");

    if (!submitBtn) return;

    submitBtn.addEventListener("click", function () {

        const form = document.getElementById("myForm");
        const formData = new FormData(form);

        fetch("assets/ajax/post.php", {
            method: "POST",
            body: new URLSearchParams(formData),
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            }
        })
        .then(r => r.text())
        .then(result => {
            document.getElementById("formResult").innerHTML = result;
        })
        .catch(err => {
            document.getElementById("formResult").innerHTML =
                "<p style='color:red;'>Error: " + err.message + "</p>";
        });
    });
});

      </script> -->
      <script src="{{ asset('inventory/js/popper.min.js')}}"></script>
      <script src="{{ asset('inventory/js/bootstrap.min.js')}}"></script>
      <!-- wow animation -->
      <script src="{{ asset('inventory/js/animate.js')}}"></script>
      <!-- select country -->
      <script src="{{ asset('inventory/js/bootstrap-select.js')}}"></script>
      <!-- owl carousel -->
      <script src="{{ asset('inventory/js/owl.carousel.js')}}"></script> 
      <!-- chart js -->


      <script src="{{ asset('inventory/js/utils.js')}}"></script>

      <!-- nice scrollbar -->
      <script src="{{ asset('inventory/js/perfect-scrollbar.min.js')}}"></script>
      <script>
         var ps = new PerfectScrollbar('#sidebar');
      </script>
      <!-- custom js -->
      <script src="{{ asset('inventory/js/custom2.js')}}"></script>
 
<script src="{{ asset('inventory/js/sweetalert2@11.js')}}"></script>
<script src="{{ asset('inventory/js/dataTable.min.js')}}"></script>


   </body>
</html>