<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
      <ul class="nav flex-column">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="{{ url('dashboard') }}">
            <span data-feather="home" class="align-text-bottom"></span>
            Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ url('article') }}">
            <span data-feather="file" class="align-text-bottom"></span>
            Articles
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ url ('categories') }}">
            <span data-feather="shopping-cart" class="align-text-bottom"></span>
            Categories
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
            <span data-feather="users"></span>
            Users
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
            <span data-feather="bar-chart-2"></span>
            Logout
          </a>
        </li>
      </ul>
    </div>
  </nav>