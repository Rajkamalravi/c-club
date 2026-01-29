<?php
taoh_get_header();
?>
<section class="error-area pb-40px pt-40px position-relative color">
    <div id="inex-container" class="inex-container"><center>
    <h1 class="main-text" style="color: #FF6000">Let's Breathe Together!</h1>
    <img src="<?php echo TAOH_OPS_PREFIX."/images/obviousbaba.png"; ?>" class="err-img">
    </center>
    <div class="text_inex-container"><h1 id="text" style="color: #FF6000">INHALE</h1></div>

    </div>
</section>

<style>
@import url('https://fonts.googleapis.com/css?family=Roboto:900');
body{margin:0px;}
/* .inex-container{
  width:100vw;
  height:50vh;
  background: linear-gradient(to bottom, #e5e5e5 0%,#e5e5e5 50%,#111111 50%,#111111 50%,#111111 100%);
  background-size:100% 200%;
  animation: moveBackground 6s ease-in-out infinite alternate;
} */

.inex-container{
  width:100vw;
  height:50vh;
  background: linear-gradient(to bottom, #e5e5e5 0%,#e5e5e5 50%,#111111 50%,#111111 50%,#111111 100%);
  background-size:100% 200%;
  animation: moveBackground 4s ease-in-out infinite normal;
}

.inex-container-exhale{
  width:100vw;
  height:50vh;
  background: linear-gradient(to bottom, #e5e5e5 0%,#e5e5e5 50%,#111111 50%,#111111 50%,#111111 100%);
  background-size:100% 200%;
  animation: moveBackground 8s ease-in-out infinite reverse;

}

.inex-container-hold{
  width:100vw;
  height:50vh;
  background:#111111;
  background-size:100% 200%;
}

@keyframes moveBackground {
    0%{background-position:0% 0%}
    100%{background-position:0% 100%}
}

.text_inex-container{
  position:absolute;
  width:100%;
  text-align:center;
  top:81%;
  font-size:80px;
  mix-blend-mode:difference;
  color:#FF6000;
}
h1{margin:0px; padding:0px; font-family: 'Roboto', sans-serif;transition:all .5s;}
</style>
<script>
var text = document.getElementById('text');

function inhale() {
    text.innerText = 'INHALE';
    text.classList.add("bye");
    $('#inex-container').removeClass('inex-container-exhale').removeClass('inex-container-hold').addClass('inex-container');
    setTimeout(hold, 4000); // After 4 seconds of inhale, switch to hold
}

function hold() {
    text.innerText = 'HOLD';
    text.classList.remove("bye");
    $('#inex-container').removeClass('inex-container').addClass('inex-container-hold');
    setTimeout(exhale, 7000); // After 7 seconds of holding, switch to exhale
}

function exhale() {
    text.innerText = 'EXHALE';
    text.classList.add("bye");
    $('#inex-container').removeClass('inex-container-hold').addClass('inex-container-exhale');
    setTimeout(inhale, 8000); // After 8 seconds of exhaling, switch back to inhale
}

inhale(); // Start with inhale
</script>

<?php taoh_get_footer();  ?>