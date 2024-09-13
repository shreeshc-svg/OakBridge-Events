@extends('frontend.layouts.app')
@section('title', 'Terms & Conditions' . ' ' . '-' . '' . $setting->site_title)
@section('keywords', $setting->site_keywords)
@section('description', $setting->site_description)
@section('content')

    <!--====== Start Breadcrumb Section ======-->
    <section class="page-banner bg_cover p-r z-1 text-white"
        style="background-image: url(public/assets/images/bg/breadcrumbs-bg.jpg);">
        <div class="container">
            <h1 class="page-title">Terms & Conditions</h1>
            <div class="page-breadcrumb">
                <ul class="breadcrumb-link">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li class="active">Terms & Conditions</li>
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
                            <h2>Terms and conditions</h2>
                        </div>
                        <div class="text-justify mb-30">
                            <p><span style="font-size:11pt"><span style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                            style="font-size:10.5pt"><span
                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                    style="color:#0b132b">These terms of service outline the rules and
                                                    regulations for the use of Garage Vaala's
                                                    Website.</span></span></span></span></span></p>

                            <p><span style="font-size:11pt"><span style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                            style="font-size:10.5pt"><span
                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                    style="color:#0b132b">By accessing this website we assume you accept
                                                    these terms of service in full. Do not continue to use Garage Vaala's
                                                    website if you do not accept all of the terms of service stated on this
                                                    page.</span></span></span></span></span></p>

                            <p><span style="font-size:11pt"><span style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                            style="font-size:10.5pt"><span
                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                    style="color:#0b132b">The following terminology applies to these Terms
                                                    of Service, Privacy Statement and Disclaimer Notice and any or all
                                                    Agreements: "Client", "You" and "Your" refers to you, the person
                                                    accessing this website and accepting the Company's terms of service.
                                                    "The Company", "Ourselves", "We", "Our" and "Us", refers to our Company.
                                                    "Party", "Parties", or "Us", refers to both the Client and ourselves, or
                                                    either the Client or ourselves. All terms refer to the offer, acceptance
                                                    and consideration of payment necessary to undertake the process of our
                                                    assistance to the Client in the most appropriate manner, whether by
                                                    formal meetings of a fixed duration, or any other means, for the express
                                                    purpose of meeting the Client's needs in respect of provision of the
                                                    Company's stated services/products, in accordance with and subject to,
                                                    prevailing law of . Any use of the above terminology or other words in
                                                    the singular, plural, capitalisation and/or he/she or they, are taken as
                                                    interchangeable and therefore as referring to
                                                    same.</span></span></span></span></span></p>

                            <p style="margin-left:48px"><span style="font-size:11pt"><span
                                        style="font-family:Calibri,&quot;sans-serif&quot;"><strong><span
                                                style="font-size:17.0pt"><span
                                                    style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                        style="color:#0b132b">Cookies</span></span></span></strong></span></span>
                            </p>

                            <p><span style="font-size:11pt"><span style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                            style="font-size:10.5pt"><span
                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                    style="color:#0b132b">We employ the use of cookies. By using Garage
                                                    Vaala's website you consent to the use of cookies in accordance with
                                                    Garage Vaala's privacy policy.</span></span></span></span></span></p>

                            <p><span style="font-size:11pt"><span style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                            style="font-size:10.5pt"><span
                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                    style="color:#0b132b">Most of the modern day interactive web sites use
                                                    cookies to enable us to retrieve user details for each visit. Cookies
                                                    are used in some areas of our site to enable the functionality of this
                                                    area and ease of use for those people visiting. Some of our affiliate /
                                                    advertising partners may also use
                                                    cookies.</span></span></span></span></span></p>

                            <p style="margin-left:48px"><span style="font-size:11pt"><span
                                        style="font-family:Calibri,&quot;sans-serif&quot;"><strong><span
                                                style="font-size:17.0pt"><span
                                                    style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                        style="color:#0b132b">License</span></span></span></strong></span></span>
                            </p>

                            <p><span style="font-size:11pt"><span style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                            style="font-size:10.5pt"><span
                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                    style="color:#0b132b">Unless otherwise stated, Garage Vaala and/or it's
                                                    licensors own the intellectual property rights for all material on
                                                    Garage Vaala. All intellectual property rights are reserved. You may
                                                    view and/or print pages from www.garagevaala.com for your own personal
                                                    use subject to restrictions set in these terms of
                                                    service.</span></span></span></span></span></p>

                            <p><span style="font-size:11pt"><span style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                            style="font-size:10.5pt"><span
                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                    style="color:#0b132b">You must not:</span></span></span></span></span>
                            </p>

                            <ul>
                                <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                    style="font-size:10.5pt"><span
                                                        style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Republish
                                                        material from www.garagevaala.com</span></span></span></span></span>
                                </li>
                                <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                    style="font-size:10.5pt"><span
                                                        style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Sell,
                                                        rent or sub-license material from
                                                        www.garagevaala.com</span></span></span></span></span></li>
                                <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                    style="font-size:10.5pt"><span
                                                        style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Reproduce,
                                                        duplicate or copy material from
                                                        www.garagevaala.com</span></span></span></span></span></li>
                            </ul>

                            <p><span style="font-size:11pt"><span style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                            style="font-size:10.5pt"><span
                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                    style="color:#0b132b">Redistribute content from Garage Vaala (unless
                                                    content is specifically made for
                                                    redistribution).</span></span></span></span></span></p>

                            <p style="margin-left:48px"><span style="font-size:11pt"><span
                                        style="font-family:Calibri,&quot;sans-serif&quot;"><strong><span
                                                style="font-size:17.0pt"><span
                                                    style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                        style="color:#0b132b">User
                                                        Comments</span></span></span></strong></span></span></p>

                            <ul>
                                <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                    style="font-size:10.5pt"><span
                                                        style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">This
                                                        Agreement shall begin on the date
                                                        hereof.</span></span></span></span></span></li>
                                <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                    style="font-size:10.5pt"><span
                                                        style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Certain
                                                        parts of this website offer the opportunity for users to post and
                                                        exchange opinions, information, material and data ('Comments') in
                                                        areas of the website. Garage Vaala does not screen, edit, publish or
                                                        review Comments prior to their appearance on the website and
                                                        Comments do not reflect the views or opinions of Garage Vaala, its
                                                        agents or affiliates. Comments reflect the view and opinion of the
                                                        person who posts such view or opinion. To the extent permitted by
                                                        applicable laws Garage Vaala shall not be responsible or liable for
                                                        the Comments or for any loss cost, liability, damages or expenses
                                                        caused and or suffered as a result of any use of and/or posting of
                                                        and/or appearance of the Comments on this
                                                        website.</span></span></span></span></span></li>
                                <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                    style="font-size:10.5pt"><span
                                                        style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Garage
                                                        Vaala reserves the right to monitor all Comments and to remove any
                                                        Comments which it considers in its absolute discretion to be
                                                        inappropriate, offensive or otherwise in breach of these Terms of
                                                        Service.</span></span></span></span></span></li>
                                <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                    style="font-size:10.5pt"><span
                                                        style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">You
                                                        warrant and represent that:</span></span></span></span></span>
                                    <ul style="list-style-type:circle">
                                        <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                        style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                            style="font-size:10.5pt"><span
                                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">You
                                                                are entitled to post the Comments on our website and have
                                                                all necessary licenses and consents to do
                                                                so;</span></span></span></span></span></li>
                                        <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                        style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                            style="font-size:10.5pt"><span
                                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">The
                                                                Comments do not infringe any intellectual property right,
                                                                including without limitation copyright, patent or trademark,
                                                                or other proprietary right of any third
                                                                party;</span></span></span></span></span></li>
                                        <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                        style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                            style="font-size:10.5pt"><span
                                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">The
                                                                Comments do not contain any defamatory, libelous, offensive,
                                                                indecent or otherwise unlawful material or material which is
                                                                an invasion of privacy</span></span></span></span></span>
                                        </li>
                                        <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                        style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                            style="font-size:10.5pt"><span
                                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">The
                                                                Comments will not be used to solicit or promote business or
                                                                custom or present commercial activities or unlawful
                                                                activity.</span></span></span></span></span></li>
                                    </ul>
                                </li>
                                <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                    style="font-size:10.5pt"><span
                                                        style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">You
                                                        hereby grant to Garage Vaala a non-exclusive royalty-free license to
                                                        use, reproduce, edit and authorize others to use, reproduce and edit
                                                        any of your Comments in any and all forms, formats or
                                                        media.</span></span></span></span></span></li>
                            </ul>

                            <p style="margin-left:48px"><span style="font-size:11pt"><span
                                        style="font-family:Calibri,&quot;sans-serif&quot;"><strong><span
                                                style="font-size:17.0pt"><span
                                                    style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                        style="color:#0b132b">Hyperlinking to our
                                                        Content</span></span></span></strong></span></span></p>

                            <ul>
                                <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                    style="font-size:10.5pt"><span
                                                        style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">The
                                                        following organizations may link to our Web site without prior
                                                        written approval:</span></span></span></span></span>

                                    <ul style="list-style-type:circle">
                                        <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                        style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                            style="font-size:10.5pt"><span
                                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Government
                                                                agencies;</span></span></span></span></span></li>
                                        <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                        style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                            style="font-size:10.5pt"><span
                                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Search
                                                                engines;</span></span></span></span></span></li>
                                        <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                        style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                            style="font-size:10.5pt"><span
                                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">News
                                                                organizations;</span></span></span></span></span></li>
                                        <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                        style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                            style="font-size:10.5pt"><span
                                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Online
                                                                directory distributors when they list us in the directory
                                                                may link to our Web site in the same manner as they
                                                                hyperlink to the Web sites of other listed businesses;
                                                                and</span></span></span></span></span></li>
                                        <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                        style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                            style="font-size:10.5pt"><span
                                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Systemwide
                                                                Accredited Businesses except soliciting non-profit
                                                                organizations, charity shopping malls, and charity
                                                                fundraising groups which may not hyperlink to our Web
                                                                site.</span></span></span></span></span></li>
                                    </ul>
                                </li>
                            </ul>

                            <ul>
                                <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                    style="font-size:10.5pt"><span
                                                        style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">These
                                                        organizations may link to our home page, to publications or to other
                                                        Web site information so long as the link: (a) is not in any way
                                                        misleading; (b) does not falsely imply sponsorship, endorsement or
                                                        approval of the linking party and its products or services; and (c)
                                                        fits within the context of the linking party's
                                                        site.</span></span></span></span></span></li>
                                <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                    style="font-size:10.5pt"><span
                                                        style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">We may
                                                        consider and approve in our sole discretion other link requests from
                                                        the following types of
                                                        organizations:</span></span></span></span></span>
                                    <ul style="list-style-type:circle">
                                        <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                        style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                            style="font-size:10.5pt"><span
                                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">commonly-known
                                                                consumer and/or business information sources such as
                                                                Chambers of Commerce, American Automobile Association, AARP
                                                                and Consumers Union;</span></span></span></span></span></li>
                                        <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                        style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                            style="font-size:10.5pt"><span
                                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">dot.com
                                                                community sites;</span></span></span></span></span></li>
                                        <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                        style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                            style="font-size:10.5pt"><span
                                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">associations
                                                                or other groups representing charities, including charity
                                                                giving sites,</span></span></span></span></span></li>
                                        <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                        style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                            style="font-size:10.5pt"><span
                                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">online
                                                                directory distributors;</span></span></span></span></span>
                                        </li>
                                        <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                        style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                            style="font-size:10.5pt"><span
                                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">internet
                                                                portals;</span></span></span></span></span></li>
                                        <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                        style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                            style="font-size:10.5pt"><span
                                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">accounting,
                                                                law and consulting firms whose primary clients are
                                                                businesses; and</span></span></span></span></span></li>
                                        <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                        style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                            style="font-size:10.5pt"><span
                                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">educational
                                                                institutions and trade
                                                                associations.</span></span></span></span></span></li>
                                    </ul>
                                </li>
                            </ul>

                            <p><span style="font-size:11pt"><span style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                            style="font-size:10.5pt"><span
                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                    style="color:#0b132b">We will approve link requests from these
                                                    organizations if we determine that: (a) the link would not reflect
                                                    unfavorably on us or our accredited businesses (for example, trade
                                                    associations or other organizations representing inherently suspect
                                                    types of business, such as work-at-home opportunities, shall not be
                                                    allowed to link); (b)the organization does not have an unsatisfactory
                                                    record with us; (c) the benefit to us from the visibility associated
                                                    with the hyperlink outweighs the absence of Garage Vaala; and (d) where
                                                    the link is in the context of general resource information or is
                                                    otherwise consistent with editorial content in a newsletter or similar
                                                    product furthering the mission of the
                                                    organization.</span></span></span></span></span></p>

                            <p><span style="font-size:11pt"><span style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                            style="font-size:10.5pt"><span
                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                    style="color:#0b132b">These organizations may link to our home page, to
                                                    publications or to other Web site information so long as the link: (a)
                                                    is not in any way misleading; (b) does not falsely imply sponsorship,
                                                    endorsement or approval of the linking party and it products or
                                                    services; and (c) fits within the context of the linking party's
                                                    site.</span></span></span></span></span></p>

                            <p><span style="font-size:11pt"><span style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                            style="font-size:10.5pt"><span
                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                    style="color:#0b132b">If you are among the organizations listed in
                                                    paragraph 2 above and are interested in linking to our website, you must
                                                    notify us by sending an e-mail to info@garagevaala.com. Please include
                                                    your name, your organization name, contact information (such as a phone
                                                    number and/or e-mail address) as well as the URL of your site, a list of
                                                    any URLs from which you intend to link to our Web site, and a list of
                                                    the URL(s) on our site to which you would like to link. Allow 2-3 weeks
                                                    for a response.</span></span></span></span></span></p>

                            <p><span style="font-size:11pt"><span style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                            style="font-size:10.5pt"><span
                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                    style="color:#0b132b">Approved organizations may hyperlink to our Web
                                                    site as follows:</span></span></span></span></span></p>

                            <ul>
                                <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                    style="font-size:10.5pt"><span
                                                        style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">By use
                                                        of our corporate name; or</span></span></span></span></span></li>
                                <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                    style="font-size:10.5pt"><span
                                                        style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">By use
                                                        of the uniform resource locator (Web address) being linked to;
                                                        or</span></span></span></span></span></li>
                                <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                    style="font-size:10.5pt"><span
                                                        style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">By use
                                                        of any other description of our Web site or material being linked to
                                                        that makes sense within the context and format of content on the
                                                        linking party's site.</span></span></span></span></span></li>
                            </ul>

                            <p><span style="font-size:11pt"><span style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                            style="font-size:10.5pt"><span
                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                    style="color:#0b132b">No use of Garage Vaala's logo or other artwork
                                                    will be allowed for linking absent a trademark license
                                                    agreement.</span></span></span></span></span></p>

                            <p style="margin-left:48px"><span style="font-size:11pt"><span
                                        style="font-family:Calibri,&quot;sans-serif&quot;"><strong><span
                                                style="font-size:17.0pt"><span
                                                    style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                        style="color:#0b132b">Iframes</span></span></span></strong></span></span>
                            </p>

                            <p><span style="font-size:11pt"><span style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                            style="font-size:10.5pt"><span
                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                    style="color:#0b132b">Without prior approval and express written
                                                    permission, you may not create frames around our Web pages or use other
                                                    techniques that alter in any way the visual presentation or appearance
                                                    of our Web site.</span></span></span></span></span></p>

                            <p style="margin-left:48px"><span style="font-size:11pt"><span
                                        style="font-family:Calibri,&quot;sans-serif&quot;"><strong><span
                                                style="font-size:17.0pt"><span
                                                    style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                        style="color:#0b132b">Content
                                                        Liability</span></span></span></strong></span></span></p>

                            <p><span style="font-size:11pt"><span style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                            style="font-size:10.5pt"><span
                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                    style="color:#0b132b">We shall have no responsibility or liability for
                                                    any content appearing on your Web site. You agree to indemnify and
                                                    defend us against all claims arising out of or based upon your Website.
                                                    No link(s) may appear on any page on your Web site or within any context
                                                    containing content or materials that may be interpreted as libelous,
                                                    obscene or criminal, or which infringes, otherwise violates, or
                                                    advocates the infringement or other violation of, any third party
                                                    rights.</span></span></span></span></span></p>

                            <p style="margin-left:48px"><span style="font-size:11pt"><span
                                        style="font-family:Calibri,&quot;sans-serif&quot;"><strong><span
                                                style="font-size:17.0pt"><span
                                                    style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                        style="color:#0b132b">Reservation of
                                                        Rights</span></span></span></strong></span></span></p>

                            <p><span style="font-size:11pt"><span style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                            style="font-size:10.5pt"><span
                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                    style="color:#0b132b">We reserve the right at any time and in its sole
                                                    discretion to request that you remove all links or any particular link
                                                    to our Web site. You agree to immediately remove all links to our Web
                                                    site upon such request. We also reserve the right to amend these terms
                                                    of service and its linking policy at any time. By continuing to link to
                                                    our Web site, you agree to be bound to and abide by these linking terms
                                                    of service.</span></span></span></span></span></p>

                            <p style="margin-left:48px"><span style="font-size:11pt"><span
                                        style="font-family:Calibri,&quot;sans-serif&quot;"><strong><span
                                                style="font-size:17.0pt"><span
                                                    style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                        style="color:#0b132b">Removal of links from our
                                                        website</span></span></span></strong></span></span></p>

                            <p><span style="font-size:11pt"><span style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                            style="font-size:10.5pt"><span
                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                    style="color:#0b132b">If you find any link on our Web site or any
                                                    linked web site objectionable for any reason, you may contact us about
                                                    this. We will consider requests to remove links but will have no
                                                    obligation to do so or to respond directly to
                                                    you.</span></span></span></span></span></p>

                            <p><span style="font-size:11pt"><span style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                            style="font-size:10.5pt"><span
                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                    style="color:#0b132b">Whilst we endeavour to ensure that the
                                                    information on this website is correct, we do not warrant its
                                                    completeness or accuracy; nor do we commit to ensuring that the website
                                                    remains available or that the material on the website is kept up to
                                                    date.</span></span></span></span></span></p>

                            <p style="margin-left:48px"><span style="font-size:11pt"><span
                                        style="font-family:Calibri,&quot;sans-serif&quot;"><strong><span
                                                style="font-size:17.0pt"><span
                                                    style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                        style="color:#0b132b">Disclaimer</span></span></span></strong></span></span>
                            </p>

                            <p><span style="font-size:11pt"><span style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                            style="font-size:10.5pt"><span
                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                    style="color:#0b132b">To the maximum extent permitted by applicable
                                                    law, we exclude all representations, warranties and conditions relating
                                                    to our website and the use of this website (including, without
                                                    limitation, any warranties implied by law in respect of satisfactory
                                                    quality, fitness for purpose and/or the use of reasonable care and
                                                    skill). Nothing in this disclaimer
                                                    will:</span></span></span></span></span></p>

                            <ul>
                                <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                    style="font-size:10.5pt"><span
                                                        style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">limit
                                                        or exclude our or your liability for death or personal injury
                                                        resulting from negligence;</span></span></span></span></span></li>
                                <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                    style="font-size:10.5pt"><span
                                                        style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">limit
                                                        or exclude our or your liability for fraud or fraudulent
                                                        misrepresentation;</span></span></span></span></span></li>
                                <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                    style="font-size:10.5pt"><span
                                                        style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">limit
                                                        any of our or your liabilities in any way that is not permitted
                                                        under applicable law; or</span></span></span></span></span></li>
                                <li><span style="font-size:11pt"><span style="color:#0b132b"><span
                                                style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                                    style="font-size:10.5pt"><span
                                                        style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">exclude
                                                        any of our or your liabilities that may not be excluded under
                                                        applicable law.</span></span></span></span></span></li>
                            </ul>

                            <p><span style="font-size:11pt"><span style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                            style="font-size:10.5pt"><span
                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                    style="color:#0b132b">The limitations and exclusions of liability set
                                                    out in this Section and elsewhere in this disclaimer: (a) are subject to
                                                    the preceding paragraph; and (b) govern all liabilities arising under
                                                    the disclaimer or in relation to the subject matter of this disclaimer,
                                                    including liabilities arising in contract, in tort (including
                                                    negligence) and for breach of statutory
                                                    duty.</span></span></span></span></span></p>

                            <p><span style="font-size:11pt"><span style="font-family:Calibri,&quot;sans-serif&quot;"><span
                                            style="font-size:10.5pt"><span
                                                style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><span
                                                    style="color:#0b132b">To the extent that the website and the
                                                    information and services on the website are provided free of charge, we
                                                    will not be liable for any loss or damage of any
                                                    nature</span></span></span></span></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@stop
