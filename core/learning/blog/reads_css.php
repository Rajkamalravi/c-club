<style>
/*body{margin-top:20px;}*/
.blog-listing {
    padding-top: 30px;
    padding-bottom: 30px;
}
.gray-dark, 
.gray-bg {
    background-color: #f5f5f5;
}
img {
    vertical-align: middle;
    border-style: none;
}
.title-hover{
  width: 100%;
}         
h4.session_title>span {
 font-family: "Droid Sans";
  font-size: 12px;
  font-weight: bold;
  color: #fff;
  background-color: #222;
  border-radius: 3px;
  padding: 2px 15px;
  position: relative;
  display: inline-table;
  vertical-align: middle;
  line-height: 17px;
  top: -2px;
}
@media screen and (min-width: 800px) {
   #changeText{
      text-align:left;
   }
}
.blog-news{
    text-align: center;
    background: #f43d2a;
    font-size: 13px;
    color:white;
    border-radius:10px;
}

@media only screen and (max-device-width: 480px) {
  .blog-news{
    margin: 0% 20%;
  }
}
.parentContainer .banner-image{
  width: 100%;
  height: 100%;
  object-fit: contain;
}
.parentContainer {
  color: #000;
  text-align: left;
}
a.parentContainer:hover {
  color: #000;
  font-weight: bold;
}
.parentContainer .top-left {
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  right:  0;
  background:rgba(0,0,0,0.6);
  height: 100%;
  width: 100%;
  padding: 20px;
  border: 2px solid #fff;
  border-radius: 12px;
  z-index:1;
}
@media screen and (max-width: 767px) {
  .parentContainer  .top-left{
    padding: 8px;
  }
}
.nav-pad{
  padding: 15px;
  padding-left: 90px;
 }
.h3-title{
  font-size: 19px;
  line-height: 23px;
}
.descrip{
  font-family: Arimo;
  font-size: 13px;
  margin-bottom: 12px;
  line-height:18px;
  display: -webkit-box;
  -webkit-line-clamp: 2;          /* Limit to 2 lines */
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}
.js-title{
    color: black;
}
.js-title :hover{
  color: green;
}
.sm-title{
  margin-left: 10px;
  font-size: 14px;
  line-height: 17px;
}
h3 a{
  color: black;
}
.sm-title :hover{
  color: green;
}
.h3-title :hover{
  color: green;
}
@media (min-width: 1024px) {
  .hero-firstrow{
    height:330px;
  }
}
#sticky {
  position: sticky;
  position: -webkit-sticky;
  top: 70px;
}
@media (max-width: 767px){
.lat-image{
    width: 100%;
}
}
@media screen and (min-width: 800px) {
  .cl-image{
    width: 100%;
    max-width:620px; 
    height:490px; 
    position:relative;
    overflow:hidden;
  }
  .cl-image img{
    position:absolute; 
    top:0; 
    bottom:0; 
    margin: auto;
    width:100%;
  }
}
.meta-data{
    font-size: 11px;
    color: black;
    font-weight: 600;
    line-height: 30px;
}
.td_module_12 {
    padding-left: 20px;
    padding-right: 19px;
}
.td-read-more {
    display: inline-block;
}
.td-read-more a {
    font-family: 'Open Sans', arial, sans-serif;
    background-color: #444;
    border-radius: 3px;
    color: #fff;
    font-size: 12px;
    font-weight: 600;
    line-height: 15px;
    padding: 8px 12px;
    display: block;
}
.td-read-more a:hover {
    background-color: #0a9e01;
    color: #fff;
}
.lat-title{
  font-family: "Droid Serif";
  font-size: 28px;
  line-height: 32px;
  margin: 0 0 7px 0;
  font-weight: 500;
}
.lat-title :hover{
  color: red;
}
.top-title{
    font-size: 21px;
    line-height: 25px;
    font-weight: 500;
    margin-bottom: 7px;
    font-family: "Droid Serif";
}
.td-post-small-box a
{
  background-color: #a5a5a5;
    margin: 4px;
    padding: 3px 3px 3px 3px;
    color: #fff;
    display: inline-block;
    font-size: 11px;
    text-transform: uppercase;
    line-height: 1;
}
.td-post-small-box a:hover{
  background-color: green;
  color:#fff;
}
.rounded-5 {
        border-radius: 0.5rem!important;
    }
    .mb-4 {
        margin-bottom: 1.5rem!important;
    }
    .shadow-2-strong {
        box-shadow: 0 0 3px 0 rgba(0,0,0,.16),0 2px 2px 0 rgba(0,0,0,.1)!important;
    }

    .img-fluid, .img-thumbnail {
        max-width: 100%;
        height: auto;
    }
    .divider{
        border-bottom: 1px solid #dee2e6!important; 
    }

.td-post-content * {
  font-size: 16px;
  color: #000000;
  line-height: 1.8;
}
.td-post-content h1,
.td-post-content h2,
.td-post-content h3,
.td-post-content h4,
.td-post-content h5,
.td-post-content h6 {
    font-size: 17.7px;
    font-weight: 600;
    /* color: #2A4E96; */
    color: #000000;
}
.td-post-content ul,
.td-post-content ol {
    margin-left: 18px;
    margin-bottom: 12px;
}
.td-post-content ol {
    list-style-type: decimal;
    list-style: decimal;
}
.td-post-content ul
{
   list-style-type: disc;
   list-style: disc;
}

.td-post-content p,.td-post-content li {
  /*font-family: Arimo;*/
  font-size: 15px;
  line-height: 30px;
}
.td-post-content p {
  /* font-family: Arimo; */
  font-size: 16px;
  line-height: 30px;
  color:black;
  margin-bottom: 24px;
}
.no-results h2{
    font-size: 27px;
    line-height: 38px;
    margin-top: 30px;
    margin-bottom: 20px;
    letter-spacing: -0.02em;
}
.td-pb-padding-side {
    padding: 0 19px 0 20px;
}
.image-box {
    position: relative;
    margin: auto;
    overflow: hidden;
    border-radius: 12px;
}
.image-box img {
    transition: all 0.3s;
    height: auto;
    transform: scale(1);
}
.image-box:hover img {
    transform: scale(1.1);
}
.td-post-template-5 header h1 {
  font-family: "Droid Serif";
  font-weight: 400;
  font-size: 32px;
  line-height: 35px;
  color: #222;
  word-wrap: break-word;
  margin-top: 20px;
}
.post-btn-v2 {
  width: 139px; 
  height: 40px; 
  font-size: 17px; 
  border-radius: 20px;
}
.search-title-v2 {
  width:200px;
}

</style>