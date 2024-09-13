@extends('frontend.layouts.app')
@section('title', 'Disclaimer' . ' ' . '-' . '' . $setting->site_title)
@section('keywords', $setting->site_keywords)
@section('description', $setting->site_description)
@section('content')

    <!--====== Start Breadcrumb Section ======-->
    <section class="page-banner bg_cover p-r z-1 text-white"
        style="background-image: url(public/assets/images/bg/breadcrumbs-bg.jpg);">
        <div class="container">
            <h1 class="page-title">Disclaimer</h1>
            <div class="page-breadcrumb">
                <ul class="breadcrumb-link">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li class="active">Disclaimer</li>
                </ul>
            </div>
        </div>
    </section>
    <!--====== End Breadcrumb Section ======-->

    <section class="about-section-shape pt-100 pb-70 p-r z-1">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="about-two_content-box content-box-gap wow fadeInRight mb-50"
                        style="visibility: visible; animation-name: fadeInRight;">
                        <div class="section-title mb-30 text-center">
                            <span class="sub-title"><span class="line line1"></span>Policy</span>
                            <h2>Disclaimer</h2>
                        </div>
                        <div class="text-justify mb-30">
                            <p><strong>Disclaimer</strong></p>

                            <p>To the maximum extent permitted by applicable law, we exclude all representations, warranties
                                and conditions relating to our website and the use of this website (including, without
                                limitation, any warranties implied by law in respect of satisfactory quality, fitness for
                                purpose and/or the use of reasonable care and skill). Nothing in this disclaimer will:</p>

                            <ul>
                                <li>limit or exclude our or your liability for death or personal injury resulting from
                                    negligence;</li>
                                <li>limit or exclude our or your liability for fraud or fraudulent misrepresentation;</li>
                                <li>limit any of our or your liabilities in any way that is not permitted under applicable
                                    law; or</li>
                                <li>exclude any of our or your liabilities that may not be excluded under applicable law.
                                </li>
                            </ul>

                            <p>The limitations and exclusions of liability set out in this Section and elsewhere in this
                                disclaimer: (a) are subject to the preceding paragraph; and (b) govern all liabilities
                                arising under the disclaimer or in relation to the subject matter of this disclaimer,
                                including liabilities arising in contract, in tort (including negligence) and for breach of
                                statutory duty.</p>

                            <p>To the extent that the website and the information and services on the website are provided
                                free of charge, we will not be liable for any loss or damage of any nature</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@stop
