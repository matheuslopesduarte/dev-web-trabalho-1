<!DOCTYPE html>
<html lang="pt-br">
<style>
  #loading-screen {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
  }

  #loading-spinner {
    border: 6px solid #f3f3f3;
    border-top: 6px solid #3498db;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 2s linear infinite;
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }
</style>
<div id="loading-screen">
    <div id="loading-spinner"></div>
  </div>
  <script>
    window.addEventListener('load', function () {
        document.getElementById('loading-screen').style.display = 'none';
    });
</script>


<?php
include $_SERVER['DOCUMENT_ROOT'] . '/configs.php';
try {
  $database = new mysqli($endereco, $usuario, $senha, $dtbs);
  if ($database->connect_error) {
    throw new mysqli_sql_exception("Não foi possível se conectar ao banco de dados: " . $database->connect_error);
  } else {
    echo "<script>console.info('conexão database ta Poggers')</script>";
  }
} catch (mysqli_sql_exception $e) {
  echo "<script>console.info('conexão database não ta Poggers')</script>";
  showPopup('Database  não conectado', 'Falha ao conectar ao database', 'Tentar novamente');
  exit;
}
session_start();
$endereco = $_SERVER['HTTP_HOST'];
$protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$url = $protocolo . $endereco;

if (isset($_POST['logout'])) {
  session_destroy();
  unset($_SESSION['loggedin']);
  unset($_SESSION['UserId']);
  setcookie('loggedin', '', time() - 3600, "/");
  setcookie('UserId', '', time() - 3600, "/");
  header("Location: " . $_SERVER['PHP_SELF']);
  exit();
}

if (isset($_COOKIE['UserId'])) {
  $UserId = $_COOKIE['UserId'];
  $query = "SELECT * FROM users WHERE UserId='$UserId'";
  $results = mysqli_query($database, $query);
  $row = mysqli_fetch_assoc($results);

  if (isset($_COOKIE['upaxmn']) && (isset($_COOKIE['upaymn'])) && (isset($_COOKIE['upazmn']))) {

    if ($_COOKIE['upaxmn'] == $row['upaxmn'] && $_COOKIE['upaymn'] == $row['upaymn'] && $_COOKIE['upaxmn'] == $row['upaxmn']) {

      $_SESSION['loggedin'] = true;
      $_SESSION['UserId'] = $_COOKIE['UserId'];

    } else {
      showPopup('Erro', 'Ocorreu um erro ao fazer login', 'ok');
      session_destroy();
      unset($_SESSION['loggedin']);
      unset($_SESSION['UserId']);
      setcookie('loggedin', '', time() - 3600, "/");
      setcookie('UserId', '', time() - 3600, "/");
      setcookie('upaxmn', '', time() - 3600, "/");
      setcookie('upaymn', '', time() - 3600, "/");
      setcookie('upazmn', '', time() - 3600, "/");
    }
  } else {
    showPopup('Erro', 'Ocorreu um erro ao fazer login', 'ok');
    session_destroy();
    unset($_SESSION['loggedin']);
    unset($_SESSION['UserId']);
    setcookie('loggedin', '', time() - 3600, "/");
    setcookie('UserId', '', time() - 3600, "/");
    setcookie('upaxmn', '', time() - 3600, "/");
    setcookie('upaymn', '', time() - 3600, "/");
    setcookie('upazmn', '', time() - 3600, "/");
  }
}

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
  $UserId = $_SESSION['UserId'];
  $query = "SELECT * FROM users WHERE UserId='$UserId'";
  $results = mysqli_query($database, $query);
  $row = mysqli_fetch_assoc($results);
  if (isset($row['UserStatus']) && $row['UserStatus'] == "banned") {
    showPopup('Banido', 'Este usuario foi banido', 'ok');
    session_destroy();
    unset($_SESSION['loggedin']);
    unset($_SESSION['UserId']);
    setcookie('loggedin', '', time() - 3600, "/");
    setcookie('UserId', '', time() - 3600, "/");
    setcookie('upaxmn', '', time() - 3600, "/");
    setcookie('upaymn', '', time() - 3600, "/");
    setcookie('upazmn', '', time() - 3600, "/");
  }

}
if (isset($row['profile_picture']) && $row['profile_picture'] !== NULL) {
  $ProfilePicture = $row['profile_picture'];
} else if (isset($row['default_picture']) && $row['default_picture'] !== NULL) {
  $ProfilePicture = $row['default_picture'];
} else {
  $ProfilePicture = NULL;
}

if (isset($row['username']) && $row['username'] !== NULL) {
  $username = $row['username'];
} else {
  $username = "Usuário";
}

if (isset($_COOKIE['UserId']) && $_COOKIE['UserId'] != null) {
  $isLogged = true;
} else {
  $isLogged = false;
}
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function () {
    $('#search').on('input', function () {
      var search = $(this).val();
      if (search.length > 0) {
        $.ajax({
          url: '<?php $url ?>/fx/search-preview.php',
          type: 'post',
          data: { search: search },
          success: function (response) {
            $('#preview').html(response);
          }
        });
      } else {
        $('#preview').html('');
      }
    });

    $('#SearchForm').on('submit', function (e) {
      var search = $('#search').val();
      if (search.length == 0) {
        e.preventDefault();
      }
    });

    $('#preview').on('click', function (event) {
      event.stopPropagation();
    });


    $('#search').on('focus', function () {
      $('#preview').css('display', 'list-item');
      $('#preview').css('box-shadow', '0px 5px 17px -3px rgba(0, 0, 0, 0.3)');
      $('.sc-dv').css('border-radius', '25px 25px 0px 0px');
      $('.sc-dv').css('background-color', 'white');
      $('.sc-dv').css('box-shadow', '0px -3px 10px -3px rgba(0, 0, 0, 0.3)');
      $('.sc-dv').css('border-bottom', '1px solid #d6d6d6');
    });

    $(document).on('click', function (event) {
      if (!$(event.target).closest('#search, #preview').length) {
        $('#preview').hide();
        $('.sc-dv').css('box-shadow', 'none');
        $('.sc-dv').css('border-radius', '25px 25px 25px 25px');
        $('.sc-dv').css('background-color', '#edf2fc');
        $('.sc-dv').css('border-bottom', 'none');
      }
    });
    $('.gb_Id').on('click', function () {
      $('#SearchForm').submit();
    });

    var SearchForm = document.getElementById('SearchForm');
    var inputSearch = document.getElementById('search');

    const sc_dv = document.querySelector('.sc-dv');
    const preview = document.querySelector('#preview');
    const resizeObserver = new ResizeObserver(() => {
      preview.style.width = sc_dv.offsetWidth + 'px';
    });
    resizeObserver.observe(sc_dv);

    const sidebara = document.querySelector('#sidebar');
    const content = document.querySelector('#content');
    const resizeObserver2 = new ResizeObserver(() => {
      content.style.height = sidebara.offsetHeight + 'px';
    });
    resizeObserver2.observe(sidebara);



    const Username = document.querySelector('#jd-fg');
    const profileInfo = document.querySelector('#jd-fk');
    const isLogged = <?php echo $isLogged ? 'true' : 'false'; ?>;
    const profileDropdown = document.querySelector('.profile-dropdown');
    const profileImg = document.querySelector('.profile img');
    const login = document.querySelector('#login')
    const editarPerfil = document.querySelector('#editar-perfil');
    const logoutform = document.querySelector('#formLogout');
    const profileLabel = document.querySelector('#profile');
    const loginbtn = document.querySelector('.jh-rb');

    if (isLogged) {

      login.style = 'display:none';

      loginbtn.style = 'display:none';
    } else {

      logoutform.style = 'display:none';
      profileLabel.style = 'display:none';

    }

    profileImg.addEventListener('click', function () {
      toggleProfileDropdown();
    });

    function toggleProfileDropdown() {
      profileDropdown.style.display = (profileDropdown.style.display === 'block') ? 'none' : 'block';
    }

    window.addEventListener('click', function (event) {
      if (!event.target.matches('.profile img')) {
        profileDropdown.style.display = 'none';
      }
    });

    var sidebar = document.getElementById("sidebar");

    var timeoutId;

    function ativarAnimacao() {
      if (!sidebar.classList.contains('opened')) {
        sidebar.classList.add("aberta");
      }

    }

    function cancelarAnimacao() {
      clearTimeout(timeoutId);
      sidebar.classList.remove("aberta");
      if (!sidebar.classList.contains('opened')) {
        $(".sub-items").slideUp("fast");
        $(".seta").removeClass("rotated");
      }
    }


    sidebar.addEventListener("mouseenter", function () {
      timeoutId = setTimeout(ativarAnimacao, 500);
    });


    sidebar.addEventListener("mouseleave", function () {

      cancelarAnimacao();

    });

    document.querySelector('.gb_Ic').addEventListener('click', function () {
      sidebarTogle();
      $(".sub-items").slideUp();
      $(".seta").removeClass("rotated");
    });


    function sidebarTogle() {

      if (window.innerWidth > 900 || sidebar.classList.contains('opened')) {
        toggleSidebar();
      } else {
        sidebar.classList.toggle('aberta');
      }
    }



    function toggleSidebar() {
      var sidebar = document.getElementById("sidebar");
      var toggleBtn = document.getElementById("toggle-btn");
      var content = document.getElementById("content");

      sidebar.classList.remove("aberta")
      sidebar.classList.toggle("opened");
      content.classList.toggle("sidebarOpened");

      if (sidebar.classList.contains("opened")) {
        content.style.marginLeft = "256px";
      } else {
        content.style.marginLeft = "72px";
      }
    }


    loginbtn.addEventListener('click', function () {
      window.location.href = "<?php echo $url . '/users/login.php?return=' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?>";
    });

    var divLogout = document.getElementById('logout-button-acount');
    divLogout.addEventListener('click', function () {
      var buttonLogout = document.getElementById('button-logout-button-acount');
      buttonLogout.click();
    });
    $(document).ready(function () {
      $(".item").click(function () {
        $(this).find(".sub-items").slideToggle();
        $(this).find(".seta").toggleClass("rotated");
      });
    });
  });

    function showLoadingScreen() {
        document.getElementById('loading-screen').style.display = 'flex';

    }
    
</script>

<head onload="head">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@700&display=swap" rel="stylesheet">
  <title>Mock News</title>
  <link rel="icon" href="<?php echo $url ?>/midia/icones/logo-round.png" type="image/x-icon">
</head>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap');

  @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@500&display=swap');


  .escuro {
    background-color: #2b2b2b !important;
    color: white !important;
  }

  .escuro2 {
    background-color: #424242 !important;
    color: white !important;
    border-bottom: none !important;
  }

  .Btn-escuro:hover {
    background-color: rgba(232, 234, 237, 0.08) !important;
  }

  ::-webkit-scrollbar {
    width: 10px;
  }

  ::-webkit-scrollbar-track {
    background: #f1f1f1;
  }

  ::-webkit-scrollbar-thumb {
    background: #888;
  }

  ::-webkit-scrollbar-thumb:hover {
    background: #555;
  }

  #preview {
    display: block;
  }

  .sc-it:focus {
    outline: none;
  }

  body {
    zoom: 0.9;
    overflow-y: hidden;
    overflow-x: hidden;
    margin: 0;
    background-color: #f7f9fc;
    padding: 0;
    font-family: Arial, sans-serif;
    min-width: 410px;
  }

  #header2 {
    width: 100%;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: start;
  }

  #header {
    height: 48px;
    padding: 8px;
    display: flex;
    align-items: center;
  }

  #a-img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
  }

  #a {
    display: flex;
    height: 40px;
    width: 183px;
    text-decoration: none;
    padding-right: 0px !important;
  }

  #a-span {
    font-size: 22px;
    width: 128px;
    color: rgb(68, 71, 70);
    font-family: "Roboto", sans-serif;
    padding-left: 8px;
    display: flex;
    align-content: center;
    flex-wrap: wrap;
  }

  .sidebar {
    width: 72px;
    height: calc(100% - 64px);
    align-items: stretch;
    color: black;
    position: absolute;
    background-color: #f7f9fc;
    top: 64px;
    left: 0;
    z-index: 999;
    transition: width 0.2s;
  }

  .sidebar.aberta {
    width: 256px;
    transition: width 0.2s;
    box-shadow: 0px 11px 10px 0px rgba(0, 0, 0, 0.14);
    left: 0px !important;
  }

  .sidebar .text {
    display: none;
    padding-left: 8px;
  }

  .sidebar.opened {
    width: 256px;
    transition: width 0.2s;
  }

  .sidebar.aberta .text {
    display: block;
  }

  .sidebar .text {
    display: none;
    padding-left: 8px;
  }

  .sidebar.opened .text {
    display: block;
  }

  @media (max-width: 600px) {

    .sidebar {
      left: -72px;
    }

    .ds-hg {
      display: block !important;
    }

    .ds-hr {
      display: none;
    }

    .jh-rb {
      height: auto !important;
      padding: 0px 0px !important;
    }

    .content {
      margin-left: 5px !important;
      margin-right: 5px !important;
      padding: 0px !important;
    }
  }

  .gb_Ic {
    height: 24px;
    padding: 12px;
    margin: 0 4px;
    background: none;
    border-radius: 50%;
  }

  .gb_Ic .headerMenu {
    height: 24px;
    background: none;
  }

  .gb_Ic:hover {
    cursor: pointer;
    background-color: #e7eaed;
  }

  .gb_Id {
    height: 24px;
    width: 24px;
    padding: 8px;
    margin: 3px 8px;
    background: none;
    border-radius: 50%;
  }

  .gb_Id:hover {
    cursor: pointer;
    background-color: #e7eaed;
  }

  .gb_Id .headerMenu {
    height: 24px;
    background: none;
  }

  .sc-dv {
    max-width: 720px;
    width: 60%;
    display: flex;
    border-radius: 180px;
    float: left;
    background-color: #edf2fc;
    align-items: center;
    justify-content: start;
    height: 48px;
    white-space: nowrap;
    margin-bottom: 0;
  }

  .sc-dv:hover {
    cursor: text;
  }

  @media screen and (max-width: 900px) {
    .sc-dv {
      width: 60%;
      margin: auto;
    }

    #a {
      width: 50px;
    }

    #a-span {
      display: none;
    }
  }

  .sc-it {
    display: flex;
    height: 20px;
    width: 70%;
    margin: 0px;
    border: none;
    background: none;
    padding: 13px 0px;
    font-size: 13px;
  }

  .sc-it:focus {
    outline: none;
  }

  .profile {
    float: right;
    display: flex;
    align-items: center;
    cursor: pointer;
    padding-right: 20px;
  }

  .profile img {
    width: 32px;
    height: 32px;
    border-radius: 50%;
  }

  .profile img:hover {
    cursor: pointer;
  }

  .profile-dropdown {
    display: none;
    position: absolute;
    top: 60px;
    right: 10px;
    z-index: 9;
    padding-top: 5px;
  }

  .profile-dropdown a {
    display: block;
    margin-bottom: 5px;
    color: #333;
    text-decoration: none;
  }

  .jh-rb {
    white-space: nowrap;
    color: #042139;
    font-family: "Roboto", sans-serif;
    font-size: 0.875rem;
    border-radius: 180px;
    margin-right: 20px;
    background-color: #c2e7ff;
    height: 48px;
    padding: 0px 20px;
    border: none;
  }

  .jh-rb:hover {
    cursor: pointer;
    box-shadow: 0 1px 3px 0 rgba(60, 64, 67, 0.302),
      0 4px 8px 3px rgba(60, 64, 67, 0.149);
  }

  .fs-fe {
    display: flex;
  }

  .sd-iu {
    background-color: white;
  }

  .ui-up {
    width: 100%;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: start;
  }

  .ui-ug {
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
  }

  .lg-dv {
    width: 100%;
  }

  .preview {
    position: fixed;
    top: 57px;
    max-width: 720px;
    max-height: 40%;
    width: 60%;
    display: flex;
    border-radius: 0 0 25px 25px;
    float: left;
    background-color: white;
    align-items: center;
    justify-content: start;
    white-space: nowrap;
    overflow-y: auto;
    z-index: 98;
    overflow-x: hidden;
    border: rgb(231, 231, 231);
  }

  .foto {
    height: 35px;
    width: 35px;
    float: left;
  }

  .conteudo2 {
    width: auto;
    border-radius: 1rem;
    padding: 16px;
    font-family: "Roboto", sans-serif;

  }

  .content {
    border-radius: 1rem;
    background-color: white;
    margin-right: 20px;
    margin-left: 72px;
    outline: none;
    overflow-y: auto;
    overflow-x: hidden;
    justify-content: center;
  }

  .content.sidebarOpened {
    margin-left: 256px;
    transition: margin-left 0.2s;
  }

  .rg-wf {
    display: flex;
    padding: 5px;
    border-radius: 50%;
  }

  .rg-wf:hover {
    background-color: #e7eaed;
  }

  .sidebar-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 25px;
    margin: 10px 12px;
    text-decoration: none;
    text-decoration: none;
    color: inherit;
    height: 40px;
    font-size: 24px;
    cursor: pointer;
    font-size: 12px;
  }

  .sidebar-icon:hover {
    background-color: #e7eaed;
  }

  .icon {
    border-radius: 50%;
  }

  .overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9998;
    display: none;
  }

  .popup {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: none;
  }

  .popup.blur,
  .overlay.blur {
    filter: blur(5px);
  }

  #a-acount {
    margin-left: 4px;
    border-radius: 28px;
    max-width: 400px;
    min-width: 240px;
    background: #f3f6fc;
    box-shadow: 0 4px 8px 3px rgba(0, 0, 0, .15), 0 1px 3px rgba(0, 0, 0, .3);
    padding-bottom: 25px;
  }

  #b-acount {
    border-radius: 24px;
    padding: 8px 8px 0px;
  }

  #div-main-user-acount {
    background: white;
    border-radius: 4px;
    display: block;
    margin-bottom: 2px;
  }

  #div-main-user-acount:first-child {
    border-radius: inherit;
  }

  #main-user-acount {
    display: inline-block;
    padding: 16px;
    vertical-align: top;
    width: calc(100% - 32px);
  }

  #main-user-info-acount {
    display: inline-block;
    margin-bottom: 1px;
    position: relative;
    vertical-align: top;
    width: 100%;
  }

  #main-user-div-picture-acount {
    float: left;
    margin: 0;
    position: relative;
    height: 64px;
  }

  .main-user-picture-acount {
    border-radius: 50%;
    height: 64px;
    width: 64px;

  }

  #c-acount {
    display: inline-block;
    margin-bottom: 0;
    margin-left: 14px;
    margin-top: 13px;
    max-width: calc(100% - 78px);
    overflow: hidden;
    text-overflow: ellipsis;
    vertical-align: top;
  }

  #username-main-acount {
    font-family: "Roboto";
    font-weight: 500;
    font-size: 14px;
    line-height: 20px;
    letter-spacing: normal;
    color: #1f1f1f;
    max-width: 250px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  #email-main-acount {
    font-family: "Roboto";
    font-weight: 400;
    font-size: 12px;
    line-height: 16px;
    letter-spacing: .1px;
    color: #444746;
    max-width: 250px;
    overflow: hidden;
    text-align: left;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  #div-config-acount {
    display: block;
    overflow: hidden;
    width: 100%;
  }

  #config-button-acount {
    font-family: "Roboto";
    font-weight: 500;
    font-size: 14px;
    line-height: 20px;
    border-radius: 8px;
    border: 1px solid #747775;
    color: #1f1f1f;
    float: right;
    margin-bottom: 9px;
    margin-top: 8px;
    max-width: 254px;
    padding: 5px 15px;
    position: relative;
    text-align: center;
    cursor: pointer;
  }

  #config-button-acount:hover {
    background: rgba(31, 31, 31, .08);
  }




  #button-acount {
    background: none;
    border: 1px solid transparent;
    box-sizing: border-box;
    cursor: pointer;
    display: block;
    height: 48px;
    position: relative;
    white-space: nowrap;
    width: 100%;
    padding: 0 15px 0 31px;
    border-bottom-left-radius: inherit;
    border-bottom-right-radius: inherit
  }

  .option-acount:hover {
    background: #e2ebf9 !important;
    cursor: pointer;
  }

  #div-add-acount {
    border-bottom-right-radius: inherit;
    border-bottom-left-radius: inherit;
  }

  #span-add-acount {
    font-family: "Google Sans", "Roboto";
    font-weight: 500;
    font-size: 13px;
    line-height: 20px;
    letter-spacing: normal;
    color: #1f1f1f;
    display: inline-block;
    margin-left: 30px;
    max-width: 250px;
    width: calc(100% - 62px);
    vertical-align: middle;
  }


  #e-acount {
    font-family: "Google Sans", "Roboto";
    font-weight: 500;
    font-size: 14px;
    line-height: 20px;
    letter-spacing: normal;
    color: #1f1f1f;
    display: inline-block;
    margin-left: 30px;
    max-width: 250px;
    width: calc(100% - 62px);
    vertical-align: middle;
  }



  #svg-logout-button-acount {
    box-sizing: border-box;
    color: #444746;
    display: inline-block;
    padding: 11px 4px;
    vertical-align: middle;
    height: 46px;
  }

  #logout-button-acount {
    padding: 0 23px 0 39px;
    background: none;
    border: 1px solid transparent;
    box-sizing: border-box;
    cursor: pointer;
    display: block;
    height: 48px;
    position: relative;
    white-space: nowrap;
    width: 100%;
  }

  #span-logout-button-acount {
    font-family: "Google Sans", "Roboto";
    font-weight: 500;
    font-size: 14px;
    line-height: 20px;
    letter-spacing: normal;
    color: #1f1f1f;
    display: inline-block;
    margin-left: 30px;
    max-width: 250px;
    width: calc(100% - 62px);
    vertical-align: middle;
  }

  #button-logout-button-acount {
    font-family: "Roboto";
    font-weight: 500;
    font-size: 14px;
    line-height: 20px;
    border: none;
    background: none;
    padding: 0;
    cursor: pointer;
  }

  .result {
    border-bottom: 5px;
    border-top: 5px;
    height: 42px;
    display: flex;
    align-items: center;
    padding: 5px;
    font-family: "Roboto", sans-serif;
    cursor: pointer;
  }
  .nothing-result {
    border-bottom: 5px;
    border-top: 5px;
    height: 42px;
    display: flex;
    align-items: center;
    padding: 5px;
    font-family: "Roboto", sans-serif;
    cursor: pointer;
  }

  .result>img {
    border-radius: 50%;
    margin: 10px;
  }

  .result:hover {
    background-color: rgb(231, 231, 231);
    border: 1px;
    border-color: blue;
  }

  .return-button {
    font-family: "Roboto";
    font-weight: 500;
    font-size: 14px;
    line-height: 20px;
    border-radius: 8px;
    border: 1px solid #747775;
    color: #1f1f1f;
    float: left;
    margin: 0 10px;
    margin-bottom: 9px;
    margin-top: 8px;
    max-width: 254px;
    padding: 5px 15px;
    position: relative;
    text-align: center;
    cursor: pointer;
  }

  .item {
    cursor: pointer;
    padding: 10px;
    border: 1px solid #ccc;
  }

  .sub-items {
    display: none;
    padding-left: 20px;
  }

  .icon-div-post-main {
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 25px;
    margin: 10px 12px;
    text-decoration: none;
    text-decoration: none;
    color: inherit;
    height: 40px;
    font-size: 24px;
    padding: 10px 8px;
    cursor: pointer;
    font-size: 12px;
  }
</style>

<body id="body">
  <?php

  function showPopup($title, $message, $btnmsg)
  {
    echo '
    <script>
    document.addEventListener("DOMContentLoaded", function() {
      function mostrarPopup() {
        document.getElementById("overlay").style.display = "block";
        document.getElementById("meuPopup").style.display = "block";
        document.getElementById("overlay").classList.add("blur");
        var els = document.querySelectorAll("body > *:not(#overlay):not(#meuPopup)");
        for (var i = 0; i < els.length; i++) {
          els[i].style.pointerEvents = "none";
        }
      }
      window.fecharPopup = function() {
        location.reload();
      }

      mostrarPopup();
    });
    </script>
    ';

    echo "<div id='overlay' class='overlay'></div>";
    echo "<div id='meuPopup' class='popup'>";
    echo "<h2>" . $title . "</h2>";
    echo "<p>" . $message . "</p>";
    echo "<button onclick='fecharPopup()'> $btnmsg </button>";
    echo "</div>";
  }

  ?>


  <header class="ui-up">
    <div class="lg-dv" id="lg-dv">
      <div id="header2">
        <div id="header">
          <div class="gb_Ic" id="MenuButton" aria-expanded="false" aria-label="Main menu" data-ogmb="1" role="button"
            tabindex="0">
            <img class="headerMenu" id="menuImg" src="<?php echo $url ?>/midia/icones/menu.png">
          </div>

          <a href="/" id="a"><img id="a-img" src="<?php echo $url ?>/midia/icones/logo.png" alt="Logo"><span id="a-span"
              style="font-family: 'League Spartan', 'sans-serif';">MOCK NEWS</span></a>
        </div>
        <form class="sc-dv" id="SearchForm" action="<?php echo $url ?>/search.php" method="get">
          <div class="gb_Id" id="SearchButton"><img class="headerMenu" id="searchImg"
              src="<?php echo $url ?>/midia/icones/search.png"></div>
          <input class="sc-it" type="text" name="query" value="" id="search" placeholder="Pesquisar" autocomplete="off">
          <div id="preview" class="preview">

          </div>
        </form>

      </div>
    </div>
    <div class="ui-ug">
      <div class="user">
        <div class="profile" id="profile">
          <div class="rg-wf" id="ProfPicture">
            <img src="data:image/jpeg;base64,<?php echo $ProfilePicture ?>" class="sd-iu" alt="Foto de Perfil"
              id="jd-fk">
          </div>
        </div>
        <div class="fs-fe">
          <button class="jh-rb" id="login" href="<?php echo $url . '/users/login.php?return=' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "
            https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?>">
            <img class="ds-hg" style="border-radius:50%;display:none;height:40px"
              src="<?php echo $url ?>/midia/icones/default.png">
            <p class="ds-hr">Fazer Login</p>
          </button>
        </div>
        <div class="profile-dropdown">

          <div id="a-acount">
            <div id="b-acount">
              <div id="div-main-user-acount">
                <div id="main-user-acount">
                  <div id="main-user-info-acount">
                    <div id="main-user-div-picture-acount"><img class="main-user-picture-acount"
                        src="data:image/jpeg;base64,<?php echo $ProfilePicture ?>" id="jd-fk">

                    </div>
                    <div id="c-acount">
                      <div id="username-main-acount">
                        <?php echo ucfirst($row['fullname']); ?>
                      </div>
                      <div id="email-main-acount">
                        <?php echo $row['email'] ?>
                      </div>
                    </div>
                  </div>
                  <div id="div-config-acount"><a href="<?php echo $url ?>/users/profile.php"
                      id="config-button-acount">Gerenciar sua Conta</a></div>
                </div>
              </div>
            </div>

            <div class="option-acount" id="logout-button-acount">

              <div id="svg-logout-button-acount">
                <svg height="24" viewBox="0 0 24 24" width="24" focusable="false" class=" NMm5M">
                  <path
                    d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z">
                  </path>
                  <path d="M0 0h24v24H0z" fill="none"></path>
                </svg>

              </div>
              <div id="span-logout-button-acount">
                <form method="post" action="" id="formLogout" style="margin-bottom: 0px;">
                  <input type="submit" name="logout" value="Sair" id="button-logout-button-acount">
                </form>
              </div>

            </div>
          </div>

        </div>
      </div>
    </div>
  </header>
  <div class="sidebar" id="sidebar">


    <?php if (isset($_COOKIE['UserId'])) {
      if ($row['UserClass'] == 'writter' || $row['UserClass'] == 'adm' || $row['UserClass'] == 'host') {
        echo '<a href="' . $url . '/88/ver-posts.php" style="background-color:#C2E7FF" class="sidebar-icon" isSelected="true" href="#"> 
            <img style="height:20px;" class="sidebar-img" src="' . $url . '/midia/icones/write.png"></img>
            <span class="text" style="font-size:12px">Gerenciar Posts</span>
        </a>';

      }
      if ($row['UserClass'] == 'adm' || $row['UserClass'] == 'host') {
        echo '<a href="' . $url . '/88/" style="background-color:#b9ffe8" hover="background-color:#abdeff" class="sidebar-icon" isSelected="true" href="#"> 
        <img style="height:20px;" class="sidebar-img" src="' . $url . '/midia/icones/admin-panel.png"></img>
        <span class="text" style="font-size:12px">Painel da adiministração</span>
      </a>';
      }
    } ?>
    <?php
    $querysidebar = "SELECT * FROM posts ORDER BY topic";
    $resultsidebar = $database->query($querysidebar);

    if ($resultsidebar->num_rows > 0) {

      $currentTopic = "";
      while ($rowsidebar = $resultsidebar->fetch_assoc()) {
        if ($rowsidebar["topic"] != $currentTopic) {
          if ($currentTopic != "") {
            echo '</div>';
          }
          echo '<div class="sidebar-icon item " style="height:auto;flex-direction: column;">
          <div style="display: flex;align-items: center;justify-content: center;">
            <img style="height:20px;" class="sidebar-img" src="' . $url . '/midia/icones/list.png"></img><span
              class="seta text">' . $rowsidebar['topic'] . '</span>
          </div>
          <ul class="sub-items">';
          $currentTopic = $rowsidebar["topic"];
        }

        echo '<li><a href="' . $url . '/' . 'posts/verPost.php?PageId=' . $rowsidebar['Pageid'] . '">' . $rowsidebar['title'] . '</a></li>';
      }

      echo '</ul>
      </div>';
    }
    ?>

  </div>
  
  <div class="content" id="content">

    <div class="conteudo2" id="conteudo2">
      <?php // não fechado pois é pra fechar nos includes?>
</body>

</html>