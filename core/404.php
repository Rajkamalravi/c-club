<?php  
taoh_get_header(); 
?>
<style>
    .color{
        background-color: #000015;
    }
  .main-text{
    color: white;
    text-align: center;
    font-size: 20pt;
    margin: 0;
    
  }
  .sub-text{
    color: white;
    text-align: center;
    font-size: 10pt;
    margin-top: 0;
    padding-top: 10px;
    padding-bottom: 20px;
  }
  .err-img{
    display: block;
    margin-left: auto;
    margin-right: auto;
    width: 15%;
  }
</style>
<!-- ================================
         START ERROR AREA
================================= -->
<section class="error-area pb-40px pt-40px position-relative color">
  <p class="main-text">404!</p>
    <img src="<?php echo TAOH_OPS_PREFIX."/images/obviousbaba.png"; ?>" class="err-img">
    <p class="main-text">In this moment of serendipity, let us ponder the wisdom of Obvious Baba:</p>
    <p class="sub-text">
      Ah, dear wanderer, the path you seek appears to be veiled in mystery. Obvious Baba
      reminds you that, much like life itself, <br />the digital realm too holds unexpected 
      twists and turns. Take a deep breath, and let your inner compass guide you back to the 
      <br />known shores.<br /><br />
      Now, gently close your eyes, count to three, and click the button below to retrace your steps. <br />May clarity and wisdom accompany you on your digital pilgrimage."</span>
    <div class="text-center p-0 mt-4 mb-4 rounded-0 bg-transparent">
        <a class="btn btn-primary text-center" href="<?php echo TAOH_SITE_URL_ROOT; ?>">Learning Awaits!</a>
    </div>
</section>
<!-- ================================
         END ERROR AREA
================================= -->

<?php taoh_get_footer();  ?>