<?php  
taoh_get_header(); 
?>
<style>
    
  .main-text{
    text-align: center;
    font-size: 15pt;
    margin: 0;
    font-family: sans-serif;
  }
  .sub-text{
    text-align: center;
    font-family: sans-serif;
    font-size: 12pt;
    margin-top: 0;
    padding-top: 10px;
    padding-bottom: 20px;
  }
  .err-img{
    display: block;
    margin-left: auto;
    margin-right: auto;
    width: 40%;
  }
  .main-div{
    margin: auto;
    width: 50%;
    padding: 10px;
  }
  .main-status{
    padding: 10px;
    margin: auto;
    width: 65%;
    color: #f27200;
    font-size: 30pt;
    font-weight: 700;
    text-align: center;
  }
  .btn-warning{
    background-color: #f27200;
    color: white;
  }
</style>
<!-- ================================
         START ERROR AREA
================================= -->
<section class="hero-area bg-white shadow-sm overflow-hidden pt-30px pb-30px" style="padding-left: 25% !important; padding-right: 25% !important;">
  <div class="container">
  <section class="error-area bg-white pb-40px pt-40px position-relative color">
    <div class="main-div">
      <p class="main-text pb-30px"style="color:#f27200; font-size:23pt">Event Status </p>
      <img src="<?php echo TAOH_SITE_URL_ROOT."/core/ig/images/campaign.png"; ?>" class="err-img">
    </div>
    <div class="main-status">
      <span>No Room Allocated Yet.</span>
    </div>
  </section>
    <p class="main-text">This could be due to various reasons - No appropriate match is established , room is full, techinal or other reasons. <br/> <span style="font-weight:700">You don't need to wait here. We will email you, if a room is assigned.</span><br/><br/>
    To continue exploring the platform to find curated career development resources - blogs, jobs and asks, click below.</p>
    <div class="text-center p-0 mt-4 mb-4 rounded-0 bg-transparent">
        <a class="btn btn-warning text-center" href="<?php echo TAOH_SITE_URL_ROOT; ?>">Explore Hires!</a>
    </div>

</div>
</section>
<!-- ================================
         END ERROR AREA
================================= -->

<?php taoh_get_footer();  ?>