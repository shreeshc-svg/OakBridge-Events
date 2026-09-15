{{-- About the Organizer and Our Team: Admin > Page Content > About; team members in Admin > Speakers / Advisors / Team --}}
@php
    $organizer = \App\Support\PageContent::get('about.organizer');
    $teamSection = \App\Support\PageContent::get('about.team');
    $organizers = \App\Models\Team::where('year', 'Organizer')->orderBy('id')->get();
@endphp
<section class="py-5">
    <div class="auto-container">
        <div class="row">
            <div class="col-md-12">
                <div class="sec-title text-center">
                    <h2>{{ $organizer['heading'] }}</h2>
                </div>
                <div class="page-rich-text">{!! $organizer['body'] !!}</div>
            </div>
        </div>
    </div>
</section>

@if ($organizers->count())
    <section class="speakers-section-three">
        <div class="auto-container">
            <div class="sec-title text-center">
                <span class="title">{{ $teamSection['eyebrow'] ?: $setting->bname }}</span>
                <h2>{{ $teamSection['heading'] }}</h2>
            </div>
            <div class="row">
                @foreach ($organizers as $member)
                    <div class="speaker-block-three col-xl-3 col-lg-4 col-md-6 col-sm-12 wow fadeInUp">
                        <div class="inner-box">
                            <div class="image-box">
                                <figure class="image"><a href="javascript:void(0)"><img
                                            src="{{ asset('public/uploads/images/team/' . $member->image) }}"
                                            alt="{{ $member->name }}"></a>
                                </figure>
                            </div>
                            <div class="info-box">
                                <h4 class="name" title="{{ $member->name }}"><a href="javascript:void(0)">{{ $member->name }}</a></h4>
                                <span class="designation small" title="{{ $member->position }}">{{ $member->position }}</span>
                            </div>
                            @php
                                $socialIcons = ['facebook' => 'fab fa-facebook-f', 'instagram' => 'fab fa-instagram', 'x' => 'fab fa-twitter', 'linkedin' => 'fab fa-linkedin-in', 'youtube' => 'fab fa-youtube'];
                                $social = is_array($member->social) ? $member->social : [];
                            @endphp
                            <div class="social-box">
                                <ul class="social-links social-icon-colored">
                                    @foreach ($socialIcons as $network => $iconClass)
                                        @if (!empty($social[$network]))
                                            <li><a target="_blank" rel="noopener" href="{{ $social[$network] }}"><span class="{{ $iconClass }}"></span></a></li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
