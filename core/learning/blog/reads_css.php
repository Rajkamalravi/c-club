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
 font-family: var(--theme-font-family, "Ubuntu", sans-serif);
  font-size: 12px;
  font-weight: bold;
  color: #fff;
  background-color: #222;
  border-radius: 3px;
  padding: 4px 16px;
  position: relative;
  display: inline-table;
  vertical-align: middle;
  line-height: 17px;
  top: -2px;
  letter-spacing: 0.5px;
}
@media screen and (min-width: 800px) {
   #changeText{
      text-align:left;
   }
}
.blog-news{
    text-align: center;
    background: #f43d2a;
    font-size: 12px;
    font-weight: 600;
    color:white;
    border-radius:10px;
    letter-spacing: 0.5px;
    padding: 2px 0;
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
  text-decoration: none;
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
  font-size: 18px;
  line-height: 24px;
  font-weight: 600;
}
.descrip{
  font-family: var(--theme-font-family, "Ubuntu", sans-serif);
  font-size: 14px;
  margin-bottom: 12px;
  line-height: 20px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  color: #555;
}
.js-title{
    color: #222;
    font-weight: 500;
}
.js-title:hover{
  color: #0a9e01;
  text-decoration: none;
}
.sm-title{
  margin-left: 10px;
  font-size: 14px;
  line-height: 18px;
}
h3 a{
  color: #222;
  transition: color 0.2s;
}
h3 a:hover{
  color: #0a9e01;
  text-decoration: none;
}
.sm-title :hover{
  color: #0a9e01;
}
.h3-title :hover{
  color: #0a9e01;
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
    border-radius: 10px;
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
    color: #666;
    font-weight: 600;
    line-height: 30px;
}
.td_module_12 {
    padding-left: 20px;
    padding-right: 19px;
    padding-top: 8px;
    padding-bottom: 8px;
    transition: background 0.15s;
    border-radius: 8px;
}
.td_module_12:hover {
    background: #fafafa;
}
.td-read-more {
    display: inline-block;
}
.td-read-more a {
    font-family: var(--theme-font-family, "Ubuntu", sans-serif);
    background-color: #444;
    border-radius: 4px;
    color: #fff;
    font-size: 12px;
    font-weight: 600;
    line-height: 15px;
    padding: 8px 14px;
    display: block;
    transition: background-color 0.2s;
}
.td-read-more a:hover {
    background-color: #0a9e01;
    color: #fff;
    text-decoration: none;
}
.lat-title{
  font-family: var(--theme-font-family, "Ubuntu", sans-serif);
  font-size: 28px;
  line-height: 34px;
  margin: 0 0 7px 0;
  font-weight: 500;
}
.lat-title :hover{
  color: red;
}
.top-title{
    font-size: 21px;
    line-height: 26px;
    font-weight: 500;
    margin-bottom: 7px;
    font-family: var(--theme-font-family, "Ubuntu", sans-serif);
}
.td-post-small-box a
{
  background-color: #a5a5a5;
    margin: 3px;
    padding: 4px 8px;
    color: #fff;
    display: inline-block;
    font-size: 11px;
    text-transform: uppercase;
    line-height: 1;
    border-radius: 3px;
    transition: background-color 0.2s;
}
.td-post-small-box a:hover{
  background-color: #0a9e01;
  color:#fff;
  text-decoration: none;
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
  font-size: 15px;
  line-height: 30px;
}
.td-post-content p {
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
  font-family: var(--theme-font-family, "Ubuntu", sans-serif);
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

/* Sidebar cards */
.card-item {
    border: none;
    border-radius: 0;
}
.card-item .card-body {
    padding: 20px 16px;
}
.card-item .card-body h3 {
    font-size: 16px;
    font-weight: 700;
    color: #222;
    margin-bottom: 8px;
}

/* Sidebar section spacing */
.col-lg-4 .border-bottom {
    border-color: #eee !important;
    padding-top: 4px;
    padding-bottom: 4px;
}

/* Article list items separator */
.hr-blurry {
    border: none;
    border-top: 1px solid #eee;
    margin: 8px 20px;
}

/* Line clamp utilities */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    color: #555;
    font-size: 14px;
    line-height: 22px;
}

/* ===== UX Improvements ===== */

/* Hero cards: glass overlay + smoother transitions */
.cover-image-container {
    border-radius: 12px;
    overflow: hidden;
    position: relative;
}
.cover-image-container .bg-image {
    transition: transform 0.4s ease;
}
.cover-image-container:hover .bg-image {
    transform: scale(1.04);
}
.parentContainer .top-left {
    background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.2) 60%, transparent 100%);
    border: none;
    border-radius: 12px;
    transition: background 0.3s;
}
.parentContainer .top-left h4 {
    font-weight: 600;
    text-shadow: 0 1px 4px rgba(0,0,0,0.4);
}

/* Trending bar polish */
.blog-news {
    border-radius: 4px;
    font-size: 11px;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    padding: 4px 12px;
}

/* Section title badges */
h4.session_title > span {
    background-color: #333;
    border-radius: 4px;
    padding: 5px 14px;
    font-size: 11px;
    letter-spacing: 0.8px;
    text-transform: uppercase;
}

/* Search bar */
.light-dark {
    background: #fff;
    z-index: 10;
}
.light-dark .form-control {
    border-radius: 8px;
    border: 1px solid #ddd;
    padding: 10px 16px;
    font-size: 14px;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.light-dark .form-control:focus {
    border-color: #0a9e01;
    box-shadow: 0 0 0 3px rgba(10,158,1,0.1);
}
.light-dark .btn {
    border-radius: 8px;
}

/* Article list items - better spacing and hover */
.td_module_12 {
    border-radius: 10px;
    padding: 12px 16px;
    margin-bottom: 4px;
}
.td_module_12:hover {
    background: #f8f8f8;
}
.td_module_12 .cl-image {
    border-radius: 8px;
}

/* Article image thumbnails */
.td_module_12 img {
    border-radius: 8px;
}

/* Read more button refinement */
.td-read-more a {
    border-radius: 6px;
    font-size: 12px;
    padding: 7px 16px;
    letter-spacing: 0.2px;
}

/* Sidebar categories (tags widget) */
.td-post-small-box a {
    border-radius: 4px;
    padding: 5px 10px;
    font-size: 11px;
    margin: 3px 2px;
    letter-spacing: 0.3px;
    background-color: #e8e8e8;
    color: #444;
    font-weight: 600;
}
.td-post-small-box a:hover {
    background-color: #0a9e01;
    color: #fff;
}

/* Sidebar section headings */
.col-lg-4 h3 {
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: #333;
    margin-bottom: 12px;
}

/* Sidebar job cards */
.col-lg-4 .border-bottom {
    padding-top: 12px;
    padding-bottom: 12px;
}

/* Sidebar card images */
.card-item img {
    border-radius: 8px;
}
.card-item .card-body {
    padding: 12px 0;
}
.card-item .card-body h3 {
    font-size: 15px;
    line-height: 20px;
}

/* Pagination styling - unified for all items */
.pagination {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 6px;
    padding: 16px 0;
    list-style: none;
    margin: 0;
}
.pagination li {
    display: inline-block;
    margin: 0 !important;
    padding: 0 !important;
}
.pagination li a,
.pagination li span,
.pagination li .page-link {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-width: 36px !important;
    height: 36px !important;
    padding: 0 10px !important;
    border-radius: 6px !important;
    font-size: 13px !important;
    font-weight: 400 !important;
    font-family: inherit !important;
    color: #555 !important;
    background: #f5f5f5 !important;
    border: none !important;
    text-decoration: none !important;
    transition: background 0.15s ease !important;
    box-sizing: border-box !important;
    line-height: 1 !important;
    margin: 0 !important;
}
.pagination li a:hover,
.pagination li .page-link:hover {
    background: #eaeaea !important;
    color: #333 !important;
    text-decoration: none !important;
}
/* Active page - subtle highlight, same size */
.pagination li.active span,
.pagination li.active a,
.pagination li.active .page-link,
.pagination li.active span.bg-primary,
.pagination li.active a.bg-primary,
.pagination li.active.bg-primary,
.pagination li .page-link.bg-primary {
    background: #e8e8e8 !important;
    color: #333 !important;
    font-weight: 500 !important;
    font-size: 13px !important;
    min-width: 36px !important;
    height: 36px !important;
    padding: 0 10px !important;
}
/* Disabled state - same size as others */
.pagination li.disabled span,
.pagination li.disabled .page-link,
.pagination li .page-link.text-gray {
    color: #bbb !important;
    background: #f5f5f5 !important;
    cursor: default !important;
    font-size: 13px !important;
    min-width: 36px !important;
    height: 36px !important;
    padding: 0 10px !important;
}
.pagination li .text-white {
    color: #333 !important;
}

/* Article description text */
.td_module_12 p,
.descrip {
    color: #555;
    font-size: 14px;
    line-height: 22px;
}

/* Separator refinement */
.hr-blurry {
    margin: 4px 16px;
    border-color: #f0f0f0;
}

/* Widget card hover */
.card-item {
    transition: background 0.2s;
    border-radius: 8px;
    padding: 4px;
}
.card-item:hover {
    background: #fafafa;
}

/* Editor's pick / What's new card grid */
.shadow-2-strong {
    border-radius: 10px !important;
    overflow: hidden;
}

/* Responsive: mobile hero */
@media (max-width: 767px) {
    .hero-firstrow,
    .hero-secondrow {
        height: 200px;
    }
    .cover-image-container {
        margin-bottom: 4px;
    }
    .parentContainer .top-left h4 {
        font-size: 14px;
        line-height: 18px;
    }
    .td_module_12 {
        padding: 10px 12px;
    }
    .pagination li a,
    .pagination li span {
        min-width: 32px;
        height: 32px;
        font-size: 13px;
    }
}

/* Smooth scroll for sidebar sticky */
#sticky {
    transition: top 0.2s;
}

/* ===== Typography consistency ===== */

/* Normalize to site font (Ubuntu) — override Droid/Arimo/Open Sans */
.blog-listing,
.blog-listing h1, .blog-listing h2, .blog-listing h3,
.blog-listing h4, .blog-listing h5, .blog-listing h6,
.blog-listing p, .blog-listing a, .blog-listing span,
.blog-listing li, .blog-listing input, .blog-listing button {
    font-family: var(--theme-font-family, "Ubuntu", sans-serif);
}

/* Section title badges — consistent small caps */
h4.session_title > span {
    font-family: var(--theme-font-family, "Ubuntu", sans-serif);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
}

/* Hero card titles */
.parentContainer .top-left h4 {
    font-size: 18px;
    font-weight: 700;
    line-height: 1.3;
    letter-spacing: -0.01em;
}
@media (min-width: 768px) {
    .parentContainer .top-left h4 {
        font-size: 22px;
        line-height: 1.25;
    }
}

/* Trending bar text */
.blog-news {
    font-weight: 700;
    font-size: 11px;
    letter-spacing: 1px;
    text-transform: uppercase;
}
#changeText {
    font-size: 14px;
    font-weight: 500;
    color: #333;
    line-height: 1.4;
}

/* Main content headings — clear hierarchy */
.h3-title {
    font-size: 16px;
    font-weight: 700;
    line-height: 1.35;
    color: #222;
    letter-spacing: -0.01em;
}
.lat-title {
    font-family: var(--theme-font-family, "Ubuntu", sans-serif);
    font-size: 24px;
    font-weight: 700;
    line-height: 1.3;
    letter-spacing: -0.02em;
    color: #222;
}
.top-title {
    font-family: var(--theme-font-family, "Ubuntu", sans-serif);
    font-size: 18px;
    font-weight: 700;
    line-height: 1.35;
    color: #222;
    letter-spacing: -0.01em;
}

/* Article list headings (h3) */
.td_module_12 h3,
.td_module_12 h3 a {
    font-size: 16px;
    font-weight: 700;
    line-height: 1.35;
    color: #222;
}

/* Article descriptions */
.descrip,
.td_module_12 p {
    font-family: var(--theme-font-family, "Ubuntu", sans-serif);
    font-size: 14px;
    font-weight: 400;
    line-height: 1.6;
    color: #555;
}

/* Sidebar headings */
.col-lg-4 h3 {
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    color: #333;
}
.col-lg-4 h5,
.col-lg-4 h5 a {
    font-size: 14px;
    font-weight: 600;
    line-height: 1.4;
    color: #222;
}

/* Sidebar card body text */
.card-item .card-body h3 {
    font-size: 15px;
    font-weight: 700;
    line-height: 1.35;
}
.card-item .card-body p,
.card-item .card-body .line-clamp-3 {
    font-size: 13px;
    font-weight: 400;
    line-height: 1.55;
    color: #666;
}

/* Category tags */
.td-post-small-box a {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.3px;
    text-transform: uppercase;
}

/* Meta/date text */
.meta-data {
    font-size: 12px;
    font-weight: 500;
    color: #888;
    letter-spacing: 0.2px;
}

/* Read more buttons */
.td-read-more a {
    font-family: var(--theme-font-family, "Ubuntu", sans-serif);
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.3px;
}

/* Trending link style */
.js-title {
    font-size: 14px;
    font-weight: 500;
    color: #333;
}

/* Search input */
.light-dark .form-control {
    font-size: 14px;
    font-weight: 400;
}
.light-dark .btn {
    font-size: 14px;
    font-weight: 600;
}

/* Blog post content — reading typography */
.td-post-content * {
    font-family: var(--theme-font-family, "Ubuntu", sans-serif);
}
.td-post-content p {
    font-size: 16px;
    font-weight: 400;
    line-height: 1.8;
    color: #222;
}
.td-post-content h1,
.td-post-content h2,
.td-post-content h3,
.td-post-content h4,
.td-post-content h5,
.td-post-content h6 {
    font-weight: 700;
    color: #111;
    letter-spacing: -0.01em;
}
.td-post-template-5 header h1 {
    font-family: var(--theme-font-family, "Ubuntu", sans-serif);
    font-size: 28px;
    font-weight: 700;
    line-height: 1.25;
    letter-spacing: -0.02em;
}

/* Pagination text */
.pagination li a,
.pagination li span {
    font-weight: 600;
    font-size: 13px;
}

/* No results */
.no-results h2 {
    font-weight: 700;
    letter-spacing: -0.02em;
}

</style>
