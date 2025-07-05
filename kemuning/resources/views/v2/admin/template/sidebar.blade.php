<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
        <!--begin::Brand Link-->
        <a href="../index.html" class="brand-link">
            <!--begin::Brand Image-->
            <img
                src="../../../dist/assets/img/AdminLTELogo.png"
                alt="AdminLTE Logo"
                class="brand-image opacity-75 shadow"
            />
            <!--end::Brand Image-->
            <!--begin::Brand Text-->
            <span class="brand-text fw-light">MUNING PHOTO</span>
            <!--end::Brand Text-->
        </a>
        <!--end::Brand Link-->
    </div>
    <!--end::Sidebar Brand-->
    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul
                class="nav sidebar-menu flex-column"
                data-lte-toggle="treeview"
                role="menu"
                data-accordion="false"
            >
                <li class="nav-header">POINT OF SALE MANAGEMENT</li>
                <li class="nav-item">
                    <a href="{{ url('admin') }}" class="nav-link {{ $pageTitle=='Admin Home' ? 'active' : '' }}">
                        <i class="nav-icon bi bi-speedometer"></i>
                        <p>HOME</p>
                    </a>
                </li>
                <li class="nav-item {{ $pageGroup=='1' ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-box-arrow-in-right"></i>
                        <p>
                            CUSTOMER
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a
                                href="{{ url('admin/customerData') }}"
                                class="nav-link {{ $pageTitle=='Customer Management' ? 'active' : '' }}"
                            >
                                <i class="nav-icon bi bi-circle"></i>
                                <p>DATA</p>
                            </a>
                        </li>
                    </ul>
                </li>
                {{-- <li class="nav-header">DOCUMENTATIONS</li> --}}
                <li class="nav-item">
                    <a href="{{ url('admin/itemData') }}" class="nav-link {{ $pageTitle=='Item Data' ? 'active' : '' }}">
                        <i class="nav-icon bi bi-diagram-3-fill"></i>
                        <p>ITEM DATA</p>
                    </a>
                </li>
            </ul>
            <!--end::Sidebar Menu-->
        </nav>
    </div>
    <!--end::Sidebar Wrapper-->
</aside>
<!--end::Sidebar-->
