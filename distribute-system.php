<?php
include "functions.php";
template_header('About')
?>
    <div class="hero-wrap hero-bread" style="background-image: url('imgs/banner_distribute.jpg');">
        <div class="container">
            <div class="row no-gutters slider-text align-items-center justify-content-center">
                <div class="col-md-9 ftco-animate text-center">
                    <!--                <p class="breadcrumbs"><span class="mr-2"><a href="index.html">Home</a></span> <span>About us</span></p>-->
                    <!--                <h1 class="mb-0 bread">About us</h1>-->
                </div>
            </div>
        </div>
    </div>

    <section class="ftco-section testimony-section">
        <div class="container">
            <div class="row justify-content-center mb-5 pb-3">
                <div class="col-md-7 heading-section ftco-animate text-center">
                    <h2 class="mb-4">DISTRIBUTION SYSTEM</h2>
                    <p>The products of Natural Flavor are being distributed at partner stores and supermarkets</p>
                </div>
            </div>
            <div class="row ftco-animate">
                <div class="col-md-12">
                    <div class="carousel-testimony owl-carousel">
                        <div class="item">
                            <div class="testimony-wrap p-4 pb-5">
                                <div class="user-img mb-5" style="background-image: url(imgs/mega.jpg)">
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <div class="testimony-wrap p-2 pb-5">
                                <div class="user-img mb-5" style="background-image: url(imgs/annam.jpg)">
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <div class="testimony-wrap p-4 pb-5">
                                <div class="user-img mb-5" style="background-image: url(imgs/bigc.jpg)">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?= template_footer() ?>