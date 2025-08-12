<div class="wrapper d-flex flex-column min-vh-100 bg-light bg-opacity-50 dark:bg-transparent">
    <header class="header header-sticky mb-4" style="padding: 50px; font-size: 24px; background-color: #001936; color: white;"> <!-- Ajusta el tamaño como desees -->
        <div class="container-fluid">
            <button class="header-toggler px-md-0 me-md-3 d-md-none" type="button" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()">
                <svg class="icon icon-lg">
                    <use xlink:href="{{ asset('../vendors/@coreui/icons/svg/free.svg#cil-menu') }}"></use>
                </svg>
            </button>
            <a class="header-brand d-md-none" href="#">
                <svg width="118" height="46" alt="CoreUI Logo">
                    <use xlink:href="{{ asset('brand/coreui.svg#full') }}"></use>
                </svg>
            </a>
        </div>
    </header>
