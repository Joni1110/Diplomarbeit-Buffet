<link rel="stylesheet" href="{{ asset('css/menu-style.css') }}">

<div class="Header">
    <div class="LogoHeader">
        <img src="images/LogoDunkel.png">
        <p>SMART BUFFET</p>
    </div>

    <nav class="navbar">
        <div class="menu"></div>
        <a href="#" class="toggle-button">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </a>

        <div class="navbar-links">
            <ul>
                <li><a href="{{ url('home') }}">Home</a></li>
                <li><a href="{{ url('bestellungen') }}">Bestellungen</a></li>
                <li><a href="{{ url('ueberuns') }}">Über uns</a></li>
                <li><a href="{{ url('kontakt') }}">Kontakt</a></li>
            </ul>
        </div>
    </nav>

    <div class="Icons">
        <img id="LogoSchuleKlein" src="images/Logo_Schule_KleinDunkel.png">

        <div class="userIconWrapper">
            <img id="UserIcon" src="images/User-IconDunkel.png" style="cursor:pointer;">
            @if(session('user_name'))
                <div id="userDropdown" class="userDropdown" style="display:none;">
                    <p>{{ session('user_name') }}</p>
                    <p>{{ session('user_email') }}</p>
                    <a href="{{ url('/logout') }}">Abmelden</a>
                </div>
            @endif
        </div>
    </div>

    <script>
        const userIcon = document.getElementById('UserIcon');
        const userDropdown = document.getElementById('userDropdown');

        userIcon.addEventListener('click', () => {
            if(userDropdown.style.display === "none") {
                userDropdown.style.display = "block";
            } else {
                userDropdown.style.display = "none";
            }
        });
    </script>

    <style>
        .userDropdown {
            position: absolute;
            background: #fff;
            border: 1px solid #ccc;
            padding: 10px;
            right: 10px;
            top: 50px;
            width: 200px;
            z-index: 999;
        }
        .userDropdown p {
            margin: 5px 0;
        }
        .userDropdown a {
            color: red;
            text-decoration: none;
        }
    </style>


    <script>
        const toggleButton = document.getElementsByClassName('toggle-button')[0];
        const navbarLinks = document.getElementsByClassName('navbar-links')[0];

        toggleButton.addEventListener('click', () => {
            navbarLinks.classList.toggle('active');
        });
    </script>
</div>
