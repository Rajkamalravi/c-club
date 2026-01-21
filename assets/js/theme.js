
$(document).ready(function () {
  // Load theme from localStorage if available
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme === 'dark') {
    applyDarkTheme();
  }else{
    applyLightTheme();
  }

  $('.themeToggle').on('click', function () {
    if ($('body').hasClass('dark-mode')) {
      applyLightTheme();
      localStorage.setItem('theme', 'light');
    } else {
      applyDarkTheme();
      localStorage.setItem('theme', 'dark');
    }
  });

  function setDarkTheme() {
    $('body').addClass('dark-mode');
    $('#mainNavbar')
      .removeClass('navbar-light bg-white')
      .addClass('navbar-dark bg-dark');
  }

  function setLightTheme() {
    $('body').removeClass('dark-mode');
    $('#mainNavbar')
      .removeClass('navbar-dark bg-dark')
      .addClass('navbar-light bg-white');
  }

function applyDarkTheme() {
  const classMap = {
    'bg-white': 'bg-dark',
    'bg-light': 'bg-dark',
    'bg-gray' : 'bg-gray-dark',
    'text-dark': 'text-light',
    'border-light': 'border-dark',
    'navbar-light': 'navbar-dark',
    'table-light': 'table-dark',
    'btn-light': 'btn-dark',
    'mobile-app' : 'mobile-app-dark',
    // 'breadcrumb-list' : 'breadcrumb-list-dark'
  };

  Object.entries(classMap).forEach(([fromClass, toClass]) => {
    $(`.${fromClass}`).each(function () {
      $(this).removeClass(fromClass).addClass(toClass);
    });
  });

  $('.layout-wrapper').attr('data-bs-theme', 'dark');
  $('body').addClass('dark-mode');
}

function applyLightTheme() {
  const classMap = {
    'bg-dark': 'bg-white',
    'bg-gray-dark':'bg-gray',
    'text-light': 'text-dark',
    'border-dark': 'border-light',
    'navbar-dark': 'navbar-light',
    'table-dark': 'table-light',
    'btn-dark': 'btn-light',
    'mobile-app-dark' : 'mobile-app',
    // 'breadcrumb-list-dark' : 'breadcrumb-list'

  };

  Object.entries(classMap).forEach(([fromClass, toClass]) => {
    $(`.${fromClass}`).each(function () {
      $(this).removeClass(fromClass).addClass(toClass);
    });
  });

  $('.layout-wrapper').attr('data-bs-theme', 'light');
  $('body').removeClass('dark-mode');
}

});