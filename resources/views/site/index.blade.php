@extends('layouts.frontend.main')

@section('title')
    Home
@endsection

@section('content')
    <!-- Banner section -->
    <section class="banner-img-section" id="home">
        <div class="img-container">
            <img src="{{ asset('assets/frontend/images/music-wave-final.svg')}}" alt="" class="img-fluid w-100 music-wave-img">
        </div>
    </section>

    <!-- See all services-->
    <section class="section section-sm section-first bg-default text-center" id="services">
        <div class="container">
            <div class="row row-30 justify-content-center">
                <div class="col-md-7 col-lg-5 col-xl-6 text-lg-left wow fadeInUp">
                    <img src="{{ asset('assets/frontend/images/tabmockup.png') }}" alt="" width="415"
                        height="592" />
                </div>
                <div class="col-lg-7 col-xl-6">
                    <div class="row row-30">
                        <div class="col-sm-6 wow fadeInRight">
                            <article class="box-icon-modern box-icon-modern-2">
                                <div class="box-icon-modern-icon linearicons-headset"></div>
                                <h5 class="box-icon-modern-title"><a href="#">Was ist <br>HiFi-Quest?</a></h5>
                                <div class="box-icon-modern-decor"></div>
                                <p class="box-icon-modern-text">HiFi-Quest ist eine cloud-basierte digitale Schnittstelle
                                    zwischen Fachhändler und Kunde.
                                </p>
                            </article>
                        </div>
                        <div class="col-sm-6 wow fadeInRight" data-wow-delay=".1s">
                            <article class="box-icon-modern box-icon-modern-2">
                                <div class="box-icon-modern-icon linearicons-loudspeaker"></div>
                                <h5 class="box-icon-modern-title"><a href="#">Was bietet <br>HiFi-Quest?</a></h5>
                                <div class="box-icon-modern-decor"></div>
                                <p class="box-icon-modern-text">HiFi-Quest digitalisiert den kompletten Handelsprozess und
                                    bietet seinen Nutzern ein im High End-Bereich noch nie da gewesenes, einzigartiges
                                    Angebot-und-Nachfrage-Portal.
                                </p>
                            </article>
                        </div>
                        <div class="col-sm-6 wow fadeInRight" data-wow-delay=".2s">
                            <article class="box-icon-modern box-icon-modern-2">
                                <div class="box-icon-modern-icon linearicons-music-note3"></div>
                                <h5 class="box-icon-modern-title"><a href="#">Was kann <br>HiFi-Quest?</a></h5>
                                <div class="box-icon-modern-decor"></div>
                                <p class="box-icon-modern-text">HiFi-Quest stellt Ihnen eine einfache
                                    Bedienoberfläche und eine Cloud- basierte Software zur Verfügung. Für die Nutzung von
                                    HiFi-Quest ist keine Installation notwendig!</p>
                            </article>
                        </div>
                        <div class="col-sm-6 wow fadeInRight" data-wow-delay=".3s">
                            <article class="box-icon-modern box-icon-modern-2">
                                <div class="box-icon-modern-icon linearicons-radio"></div>
                                <h5 class="box-icon-modern-title"><a href="#">Warum <br>HiFi-Quest?</a></h5>
                                <div class="box-icon-modern-decor"></div>
                                <p class="box-icon-modern-text">HiFi-Quest verbindet die Liebhaber des besten Klangs. HiFi-Quest ermöglicht dem Kunden und dem Fachhändler ein
                                    unkompliziertes Miteinander-in-Kontakt-Treten.</p>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cta-->
    <section class="section section-fluid bg-default" id="features">
        <div class="parallax-container" data-parallax-img="{{ asset('assets/frontend/images/cta-bg.jpg') }}">
            <div class="parallax-content section-xl context-dark bg-overlay-68 bg-mobile-overlay">
                <div class="container">
                    <div class="row row-30 justify-content-end text-right">
                        <div class="col-sm-7">
                            <h3 class="wow fadeInLeft">Für alle, die wissen, was sie suchen</h3>
                            <div class="group-sm group-middle group justify-content-end">
                                <a class="button button-primary button-ujarak" href="#modalCta" data-toggle="modal">Get
                                    in Touch</a>
                                {{-- <a class="button button-white-outline button-ujarak" href="#">mehr erfahren?</a> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Projects-->
    <section class="section section-sm section-fluid bg-default text-center" id="projects">
        <div class="container-fluid">
            <div class="row row-30 isotope" data-isotope-layout="fitRows" data-isotope-group="gallery"
                data-lightgallery="group">
                <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4 isotope-item wow fadeInRight" data-filter="Type 4">
                    <!-- Thumbnail Classic-->
                    <article class="thumbnail thumbnail-classic thumbnail-md">
                        <div class="thumbnail-classic-figure"><img
                                src="{{ asset('assets/frontend/images/screenshot/Screenshot_1.2.png') }}"
                                alt="" width="420" height="350">
                        </div>
                    </article>
                </div>
                <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4 isotope-item wow fadeInRight" data-filter="Type 1"
                    data-wow-delay=".1s">
                    <!-- Thumbnail Classic-->
                    <article class="thumbnail thumbnail-classic thumbnail-md">
                        <div class="thumbnail-classic-figure"><img
                                src="{{ asset('assets/frontend/images/screenshot/Screenshot_2.1.png') }}"
                                alt="" width="420" height="350" />
                        </div>
                    </article>
                </div>
                <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4 isotope-item wow fadeInRight" data-filter="Type 2"
                    data-wow-delay=".2s">
                    <!-- Thumbnail Classic-->
                    <article class="thumbnail thumbnail-classic thumbnail-md">
                        <div class="thumbnail-classic-figure"><img
                                src="{{ asset('assets/frontend/images/screenshot/Screenshot_3.2.png') }}"
                                alt="" width="420" height="350" />
                        </div>
                    </article>
                </div>
                <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4 isotope-item wow fadeInRight" data-filter="Type 3"
                    data-wow-delay=".3s">
                    <!-- Thumbnail Classic-->
                    <article class="thumbnail thumbnail-classic thumbnail-md">
                        <div class="thumbnail-classic-figure"><img
                                src="{{ asset('assets/frontend/images/screenshot/Screenshot_4.2.png') }}"
                                alt="" width="420" height="350" />
                        </div>
                    </article>
                </div>
                <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4 isotope-item wow fadeInLeft" data-filter="Type 3">
                    <!-- Thumbnail Classic-->
                    <article class="thumbnail thumbnail-classic thumbnail-md">
                        <div class="thumbnail-classic-figure"><img
                                src="{{ asset('assets/frontend/images/screenshot/Screenshot_5.2.png') }}"
                                alt="" width="420" height="350" />
                        </div>
                    </article>
                </div>
                <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4 isotope-item wow fadeInLeft" data-filter="Type 1"
                    data-wow-delay=".1s">
                    <!-- Thumbnail Classic-->
                    <article class="thumbnail thumbnail-classic thumbnail-md">
                        <div class="thumbnail-classic-figure"><img
                                src="{{ asset('assets/frontend/images/screenshot/Screenshot_6.1.png') }}"
                                alt="" width="420" height="350" />
                        </div>
                    </article>
                </div>
                {{-- <div class="col-sm-6 col-lg-4 col-xxl-3 isotope-item wow fadeInLeft" data-filter="Type 2"
                    data-wow-delay=".2s">
                    <!-- Thumbnail Classic-->
                    <article class="thumbnail thumbnail-classic thumbnail-md">
                        <div class="thumbnail-classic-figure"><img
                                src="{{ asset('assets/frontend/images/screenshot/Screenshot_7.png') }}"
                                alt="" width="420" height="350" />
                        </div>
                    </article>
                </div>
                <div class="col-sm-6 col-lg-4 col-xxl-3 isotope-item wow fadeInLeft" data-filter="Type 3"
                    data-wow-delay=".3s">
                    <!-- Thumbnail Classic-->
                    <article class="thumbnail thumbnail-classic thumbnail-md">
                        <div class="thumbnail-classic-figure"><img
                                src="{{ asset('assets/frontend/images/screenshot/Screenshot_8.png') }}"
                                alt="" width="420" height="350" />
                        </div>
                    </article>
                </div>
                <div class="col-sm-6 col-lg-4 col-xxl-3 isotope-item wow fadeInLeft" data-filter="Type 3"
                    data-wow-delay=".3s">
                    <!-- Thumbnail Classic-->
                    <article class="thumbnail thumbnail-classic thumbnail-md">
                        <div class="thumbnail-classic-figure"><img
                                src="{{ asset('assets/frontend/images/screenshot/Screenshot_9.png') }}"
                                alt="" width="420" height="350" />
                        </div>
                    </article>
                </div> --}}
            </div>
        </div>
    </section>

    <!-- Years of experience-->
    <section class="section section-sm bg-default" id="experience">
        <div class="container">
            <div class="row row-30 row-xl-24 justify-content-center align-items-center align-items-lg-center text-left">
                <div class="col-md-6 col-lg-5 col-xl-4 text-center"><a class="text-img" href="#">
                        <div id="particles-js"></div><span class="counter">10</span>
                    </a></div>
                <div class="col-sm-8 col-md-6 col-lg-5 col-xl-4">
                    <div class="text-width-extra-small offset-top-lg-24 wow fadeInUp">
                        <h3 class="title-decoration-lines-left">Jahre <br>Leidenschaft</h3>
                        {{-- <p class="text-gray-500">RatherApp is a team of highly experienced app designers and
                            developers creating
                            unique software for you.</p><a class="button button-secondary button-pipaluk"
                            href="#">Get in touch</a> --}}
                    </div>
                </div>
                <div class="col-sm-10 col-md-8 col-lg-6 col-xl-4 wow fadeInRight" data-wow-delay=".1s">
                    <div class="row justify-content-center flex-column offset-top-xl-26 dynamic-product-display">
                        <div class="col-9 col-sm-6 m-auto">
                            <div class="counter-amy">
                                <div class="counter-amy-number"><span class="counter">{{ $brandCount }}</span>
                                </div>
                                <h6 class="counter-amy-title">Marken</h6>
                            </div>
                        </div>
                        <div class="col-9 col-sm-6 m-auto border-top-column">
                            <div class="counter-amy">
                                <div class="counter-amy-number"><span class="counter">{{ $productCount }}</span>
                                </div>
                                <h6 class="counter-amy-title">Produkte</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-12 align-self-center">
                    <div class="row row-30 justify-content-center">
                        <div id="experience_slider" class="owl-carousel" data-autoplay="true">
                            <div class="items">
                                <a class="clients-classic" href="#">
                                    <img src="{{ asset('assets/frontend/images/logos/raidho-acoustics-vector-logo.jpg.png') }}" alt="" width="270" height="117" />
                                </a>
                            </div>
                            <div class="items">
                                <a class="clients-classic" href="#">
                                    <img src="{{ asset('assets/frontend/images/logos/nordost_logo_2019_for_website_300dpi.png') }}" alt="" width="270" height="117"/>
                                </a>
                            </div>
                            <div class="items">
                                <a class="clients-classic" href="#">
                                    <img src="{{ asset('assets/frontend/images/logos/Linn-Logo.png') }}" alt="" width="270" height="117" />
                                </a>
                            </div>
                            <div class="items">
                                <a class="clients-classic" href="#">
                                    <img src="{{ asset('assets/frontend/images/logos/esoteric_logo_black.png') }}" alt="" width="270" height="117" />
                                </a>
                            </div>
                            <div class="items">
                                <a class="clients-classic" href="#">
                                    <img src="{{ asset('assets/frontend/images/logos/DALI LOGO.png') }}" alt="" width="270" height="117" />
                                </a>
                            </div>
                            <div class="items">
                                <a class="clients-classic" href="#">
                                    <img src="{{ asset('assets/frontend/images/logos/logo_red.png') }}" alt="" width="270" height="117" />
                                </a>
                            </div>
                            <div class="items">
                                <a class="clients-classic" href="#">
                                    <img src="{{ asset('assets/frontend/images/logos/gryphon_logo_red_web_edited.png') }}" alt="" width="270" height="117" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Pricing-->
    <section class="section section-sm section-bottom-70 section-fluid bg-default" id="pricing">
        <div class="container">
            <h2>Pricing</h2>
            <div class="row row-30 justify-content-center">
                <div class="col-md-6 col-lg-5 col-xl-4">
                    <div class="box-pricing box-pricing-black">
                        <div class="box-pricing-body">
                            <h5 class="box-pricing-title">Kunde <br/> kostenlos</h5>
                            <h3 class="box-pricing-price">0 €</h3>
                            <div class="box-pricing-time" style="text-transform: capitalize">Month</div>
                            {{-- <div class="box-pricing-divider">
                                <div class="divider"></div><span>Basic</span>
                            </div> --}}
                            <ul class="box-pricing-list">
                                <li class="active">unverbindliche Kaufanfragen</li>
                                <li class="active">Hörtermin-Buchung</li>
                                <li class="active">Video-Beratung</li>
                                <li class="active">Online-Zahlung</li>
                            </ul>
                        </div>
                        <div class="box-pricing-button"><a class="button button-lg button-block button-gray-4"
                                href="#">Registrieren</a></div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-5 col-xl-4">
                    <div class="box-pricing box-pricing-black box-pricing-popular">
                        <div class="box-pricing-body">
                            <h5 class="box-pricing-title">Händler <br/> BASIC</h5>
                            <h3 class="box-pricing-price">99 €</h3>
                            <div class="box-pricing-time" style="text-transform: capitalize">Month</div>
                            {{-- <div class="box-pricing-divider">
                                <div class="divider"></div><span>Optimal</span>
                            </div> --}}
                            <ul class="box-pricing-list">
                                <li class="active">Preisangebot senden (anonym)</li>
                                <li class="active">Hörterminverwaltung</li>
                                <li class="active">Kalenderfunktion</li>
                                <li class="active">Portfolioverwaltung</li>
                            </ul>
                        </div>
                        <div class="box-pricing-button"><a class="button button-lg button-block button-primary"
                                href="#">Registrieren</a></div>
                        <div class="box-pricing-badge">popular</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-5 col-xl-4">
                    <div class="box-pricing box-pricing-black">
                        <div class="box-pricing-body">
                            <h5 class="box-pricing-title">Händler <br/> Premium</h5>
                            <h3 class="box-pricing-price">149 €</h3>
                            <div class="box-pricing-time" style="text-transform: capitalize">Month</div>
                            {{-- <div class="box-pricing-divider">
                                <div class="divider"></div><span>Ultimate</span>
                            </div> --}}
                            <ul class="box-pricing-list">
                                <li class="active">alle Basicfunktionen</li>
                                <li class="active">Video-Beratung</li>
                                <li class="active">Marktanalyse-Tool</li>

                            </ul>
                        </div>
                        <div class="box-pricing-button"><a class="button button-lg button-block button-gray-4"
                                href="#">Registrieren</a></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Advantages Of HiFi Quest-->
    <section class="section section-sm bg-default text-md-left">
        <div class="container">
            <div class="row">
                <div class="col-md-12 wow fadeInLeft">
                    <h2 class="text-center">Die Vorteile von HiFi-Quest</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                      <li class="nav-item">
                        <a class="nav-link active" id="tab_01" data-toggle="tab" href="#tab_1" role="tab" aria-controls="tab_1" aria-selected="true">Fachhändler</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" id="tab_02" data-toggle="tab" href="#tab_2" role="tab" aria-controls="tab_2" aria-selected="false">Kunde</a>
                      </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                      <div class="mt-2 tab-pane fade show active" id="tab_1" role="tabpanel" aria-labelledby="tab_1-tab">
                        <ul class="list-with-circle">
                            <li>Umsatzsteigerung durch Digitalisierung</li>
                            <li>Faire monatliche Gebühr</li>
                            <li>Kein Online-Shop mehr nötig</li>
                            <li>Umsatzorientiertes Provisionsmodell</li>
                            <li>Persönlicher Kalender</li>
                            <li>Eigene Kontaktdatenbank</li>
                            <li>Serviceorientiertes Bewertungsmodell</li>
                            <li>Einfaches Portfoliomanagement</li>
                        </ul>
                      </div>
                      <div class="mt-2 tab-pane fade" id="tab_2" role="tabpanel" aria-labelledby="tab_2-tab">
                        <ul class="list-with-circle">
                            <li>Produktspezifische Anbindung. zum Händler</li>
                            <li>Unkomplizierte Kaufabwicklung</li>
                            <li>Digitale Hörtermin-Buchung</li>
                            <li>Wachsendes Markenportfolio</li>
                            <li>Kostenloses Nutzen</li>
                            <li>Einzigartige User-Experience</li>
                            <li>Einfache Bedienung</li>
                        </ul>
                      </div>
                    </div>
                    <p class="text-gray-500">
                        <a class="button button-secondary button-pipaluk"href="#">Get in touch</a>
                    </p>
                </div>
                <div class="col-md-6 col-lg-5 col-xl-6 text-lg-left wow fadeInUp">
                    <img src="{{ asset('assets/frontend/images/tabmockup.png') }}" alt="" width="415" height="592" />
                </div>
            </div>
        </div>
    </section>



     <!-- Meet The Team-->
    <section class="section section-sm section-fluid bg-default w-75 m-auto" id="team">
        <div class="container-fluid">
            <h2>Meet The Team</h2>
            <div class="row row-sm row-30 justify-content-around">
                <div class="col-md-6 col-lg-5 col-xl-4 wow fadeInRight" data-wow-delay=".2s">
                    <!-- Team Classic-->
                    <article class="team-classic team-classic-lg"><a class="team-classic-figure" href="#"><img
                                src="{{ asset('assets/frontend/images/Unknown3.webp') }}" alt="" width="420"
                                height="424" /></a>
                        <div class="team-classic-caption">
                            <h4 class="team-classic-name"><a href="#">Chris </a></h4>
                            <p class="team-classic-status">Co-Founder / CEO</p>
                        </div>
                    </article>
                </div>
                {{-- <div class="col-md-6 col-lg-5 col-xl-4 wow fadeInRight">
                    <!-- Team Classic-->
                    <article class="team-classic team-classic-lg"><a class="team-classic-figure" href="#"><img
                                src="{{ asset('assets/frontend/images/Unknown.webp') }}" alt="" width="420"
                                height="424" /></a>
                        <div class="team-classic-caption">
                            <h4 class="team-classic-name"><a href="#">Pino</a></h4>
                            <p class="team-classic-status">Marketing Director</p>
                        </div>
                    </article>
                </div> --}}
                <div class="col-md-6 col-lg-5 col-xl-4 wow fadeInRight" data-wow-delay=".1s">
                    <!-- Team Classic-->
                    <article class="team-classic team-classic-lg"><a class="team-classic-figure" href="#"><img
                                src="{{ asset('assets/frontend/images/Unknown2.webp') }}" alt="" width="420"
                                height="424" /></a>
                        <div class="team-classic-caption">
                            <h4 class="team-classic-name"><a href="#">Sam </a></h4>
                            <p class="team-classic-status">Co-Founder / CEO</p>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form-->
    <section class="section section-sm section-last bg-default text-left" id="contacts">
        <div class="container">
            <article class="title-classic">
                <div class="title-classic-title">
                    <h3>Get in touch</h3>
                </div>
                <div class="title-classic-text">
                    <p>If you have any questions, just fill in the contact form, and we will answer you shortly.</p>
                </div>
            </article>
            <form class="rd-form rd-form-variant-2 rd-mailform" data-form-output="form-output-global"
                data-form-type="contact" method="post" action="{{route('contact')}}">
                <div class="row row-14 gutters-14">
                    <div class="col-md-4">
                        <div class="form-wrap">
                            <input class="form-input" id="contact-your-name-2" type="text" name="name"
                                data-constraints="@Required">
                            <label class="form-label" for="contact-your-name-2">Your Name</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-wrap">
                            <input class="form-input" id="contact-email-2" type="email" name="email"
                                data-constraints="@Email @Required">
                            <label class="form-label" for="contact-email-2">E-mail</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-wrap">
                            <input class="form-input" id="contact-phone-2" type="text" name="phone"
                                data-constraints="@Numeric">
                            <label class="form-label" for="contact-phone-2">Phone</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-wrap">
                            <label class="form-label" for="contact-message-2">Message</label>
                            <textarea class="form-input textarea-lg" id="contact-message-2" name="message" data-constraints="@Required"></textarea>
                        </div>
                    </div>
                </div>
                <button class="button button-primary button-pipaluk" type="submit">Send Message</button>
            </form>
        </div>
    </section>
@endsection

@section('script-bottom')
    <script>
        $(document).ready(function() {
            $("#experience_slider").owlCarousel({
				items:4,
                loop:true,
				margin:10,
				nav:false,
				dots:false,
				autoplay:true,
				autoplayTimeout:1000,
				autoplayHoverPause:false
            });
            window.addEventListener('scroll', function() {
                var scrollPosition = window.scrollY || window.pageYOffset;
                // Adjust the margin bottom when reaching the end of the page
                if (scrollPosition + window.innerHeight >= document.documentElement.scrollHeight) {
                    $('.ui-to-top').addClass('end-of-position');
                }
                else{
                    $('.ui-to-top').removeClass('end-of-position');
                }
            });
        });
    </script>
@endsection
