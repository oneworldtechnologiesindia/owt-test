<!-- Page Footer-->
<footer class="section section-fluid footer-minimal context-dark">
    <div class="bg-gray-15">
        <div class="container-fluid">
            {{-- <div class="footer-minimal-inset oh">
                <ul class="footer-list-category-2">
                    <li><a href="#">UI Design</a></li>
                    <li><a href="#">Windows/Mac OS Apps</a></li>
                    <li><a href="#">Android/iOS Apps</a></li>
                    <li><a href="#">Cloud Solutions</a></li>
                    <li><a href="#">Customer Support</a></li>
                </ul>
            </div> --}}
            <div class="footer-minimal-bottom-panel text-sm-left">
                <div class="row row-10 align-items-md-center">
                    <div class="col-sm-6 col-md-4 text-sm-right text-md-center">
                        <div>
                            {{-- <ul class="list-inline list-inline-sm footer-social-list-2">
                                <li><a class="icon fa fa-facebook" href="#"></a></li>
                                <li><a class="icon fa fa-twitter" href="#"></a></li>
                                <li><a class="icon fa fa-google-plus" href="#"></a></li>
                                <li><a class="icon fa fa-instagram" href="#"></a></li>
                            </ul> --}}
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 order-sm-first">
                        <!-- Rights-->
                        <p class="rights"><span>&copy;&nbsp;</span><span class="copyright-year"></span>
                            <span>{{ config('app.name') }}</span>
                        </p>
                    </div>
                    <div class="col-sm-6 col-md-4 text-md-right"><span>All rights reserved.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<div class="modal fade" id="modalCta" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Contact Us</h4>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <form class="rd-form rd-form-variant-2 rd-mailform" data-form-output="form-output-global"
                    data-form-type="contact-modal" method="post" action="{{route('contact')}}">
                    <div class="row row-14 gutters-14">
                        <div class="col-12">
                            <div class="form-wrap">
                                <input class="form-input" id="contact-modal-name" type="text" name="name"
                                    data-constraints="@Required">
                                <label class="form-label" for="contact-modal-name">Your Name</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-wrap">
                                <input class="form-input" id="contact-modal-email" type="email" name="email"
                                    data-constraints="@Email @Required">
                                <label class="form-label" for="contact-modal-email">E-mail</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-wrap">
                                <input class="form-input" id="contact-modal-phone" type="text" name="phone"
                                    data-constraints="@Numeric">
                                <label class="form-label" for="contact-modal-phone">Phone</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-wrap">
                                <label class="form-label" for="contact-modal-message">Message</label>
                                <textarea class="form-input textarea-lg" id="contact-modal-message" name="message" data-constraints="@Required"></textarea>
                            </div>
                        </div>
                    </div>
                    <button class="button button-primary button-pipaluk" type="submit">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</div>
