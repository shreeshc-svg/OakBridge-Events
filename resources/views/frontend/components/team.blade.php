<div class="container-fluid team pb-5">
    <div class="container pb-5">
        <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
            <h4 class="text-primary">Our Team</h4>
            <h1 class="display-4">Our Company Dedicated Team Members</h1>
        </div>
        <div class="row g-4 justify-content-center">
            @foreach ($teams as $team)
                <div class="col-sm-6 col-md-6 col-lg-4  wow fadeInUp" data-wow-delay="0.1s">
                    <div class="team-item rounded">
                        <div class="team-img">
                            <img src="{{ asset('public/uploads/images/team/' . $team->image) }}"
                                class="img-fluid w-100 rounded-top" alt="Image">
                        </div>
                        <div class="team-content bg-dark text-center rounded-bottom p-4">
                            <div class="team-content-inner rounded-bottom">
                                <h5 class="text-white">{{ $team->name }}</h6>
                                <p class="text-muted mb-3">{{ $team->position }}</p>
                                <div id="teamContent">
                                    {{ $team->bio }}
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</div>




