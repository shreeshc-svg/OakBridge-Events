<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables behind Admin > Website Content (sponsors, page text, Legathon,
     * menus, SEO). Seeds them with what the site shows today so nothing
     * changes on the public pages until an admin edits something.
     */
    public function up(): void
    {
        Schema::create('sponsor_groups', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->nullable();          // page anchor, e.g. #exhibitors
            $table->string('logo_size', 10)->default('small'); // large | medium | small
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });

        Schema::create('sponsors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sponsor_group_id')->constrained('sponsor_groups')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('logo');                       // path from the site root
            $table->string('url', 500)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });

        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('data')->nullable();         // JSON
            $table->timestamps();
        });

        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->text('buttons')->nullable();          // JSON
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });

        Schema::create('page_seo', function (Blueprint $table) {
            $table->id();
            $table->string('page')->unique();             // route name
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('keywords')->nullable();
            $table->timestamps();
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('location', 20)->default('header'); // header | footer
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->cascadeOnDelete();
            $table->string('label');
            $table->string('url', 500)->nullable();
            $table->boolean('new_tab')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });

        $this->seedSponsors();
        $this->seedCompetitions();
        $this->seedMenus();
        $this->seedOrganizerTeam();
    }

    public function down(): void
    {
        DB::table('teams')->where('year', 'Organizer')->delete();
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('page_seo');
        Schema::dropIfExists('competitions');
        Schema::dropIfExists('page_sections');
        Schema::dropIfExists('sponsors');
        Schema::dropIfExists('sponsor_groups');
    }

    private function seedSponsors(): void
    {
        $groups = [
            ['Powered By', 'large', ['samco']],
            ['Platinum Partner', 'medium', ['legora']],
            ['Technology Partner', 'medium', ['lexisnexis']],
            ['Gold Partners', 'medium', ['lks', 'counselect', 'lexplosion', 'legalleague']],
            ['Silver Partner', 'small', ['sama']],
            ['Strategic Partners', 'small', ['silf', 'sadgamaya', 'ccai', 'acosindia', 'ficl']],
            ['International Strategic Partner', 'medium', ['alita']],
            ['Law School Partner', 'small', ['dhirubai']],
            ['Ecosystem Partner', 'small', ['iltn']],
            ['Legal Insights Partner', 'small', ['barbench']],
            ['Digital Media Partner', 'small', ['lawbeat']],
            ['Media Partners', 'small', ['bar-bulleting', 'lex-witness']],
            ['Board Alliance Partner', 'small', ['ybp']],
            ['Supporting Partners', 'small', ['lawyered', 'teres', 'lite-lab-hku', 'metpro', 'lexel', 'wyp', 'quatro']],
            ['Exhibitors', 'small', ['legora', 'lexisnexis', 'counselect', 'trackase', 'complinity', 'taxo']],
        ];

        $now = now();
        foreach ($groups as $g => [$title, $size, $logos]) {
            $groupId = DB::table('sponsor_groups')->insertGetId([
                'title' => $title,
                'slug' => \Illuminate\Support\Str::slug($title),
                'logo_size' => $size,
                'sort_order' => $g + 1,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            foreach ($logos as $i => $file) {
                DB::table('sponsors')->insert([
                    'sponsor_group_id' => $groupId,
                    'name' => ucwords(str_replace('-', ' ', $file)),
                    'logo' => 'public/assets/images/logos/' . $file . '.webp',
                    'url' => null,
                    'sort_order' => $i + 1,
                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function seedCompetitions(): void
    {
        $form = 'https://docs.google.com/forms/d/e/1FAIpQLSepNNESudMWJNgjsSA680K6I73DZtgCJfUqYnEDzyEh7aMd9g/viewform';
        $now = now();

        DB::table('competitions')->insert([
            [
                'title' => 'Legislation Drafting Competition',
                'subtitle' => 'Fali Nariman Memorial Legathon',
                'description' => '<p>Participants are invited to draft a legislation titled – <strong>“Public Service Efficiency Act: Standards, Metrics, and Enforcement”</strong></p>'
                    . '<p>The proposed legislation should:</p><ul>'
                    . '<li>Identify core public services (e.g., healthcare, education, infrastructure maintenance, water, and sanitation) that require standardized service delivery metrics.</li>'
                    . '<li>Define clear, measurable performance standards and timelines for these services.</li>'
                    . '<li>Incorporate an accountability structure, outlining the roles of agencies in monitoring and enforcing standards.</li>'
                    . '<li>Propose penalties and incentives to encourage compliance and maintain service quality.</li></ul>',
                'image' => 'public/assets/legathan/drafting-image.webp',
                'buttons' => json_encode([
                    ['label' => 'Learn More', 'url' => '/public/assets/legathan/fali-nariman.pdf', 'style' => 'one', 'new_tab' => true],
                    ['label' => 'Click to Register', 'url' => $form, 'style' => 'two', 'new_tab' => true],
                ]),
                'sort_order' => 1,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Lights, Camera & Justice',
                'subtitle' => 'Short Video Making Competition',
                'description' => '<p>Participants are invited to create a short video that delves into one of the following sub-themes:</p><ul>'
                    . '<li>Judicial Activism in India</li>'
                    . '<li>Free Speech in the Digital Age: Challenges in India</li>'
                    . '<li>Role of Law &amp; Justice in Improving Accessibility of Justice</li>'
                    . '<li>Consumer Law Issues</li>'
                    . '<li>Role of AI in Law</li>'
                    . '<li>Justice in Couplets: Shayari mein Kanoon ki Kahani</li></ul>',
                'image' => 'public/assets/legathan/video-image.webp',
                'buttons' => json_encode([
                    ['label' => 'Learn More', 'url' => '/public/assets/legathan/light-camera-action.pdf', 'style' => 'one', 'new_tab' => true],
                    ['label' => 'Click to Register', 'url' => $form, 'style' => 'two', 'new_tab' => true],
                    ['label' => 'Workshop by Aditya Bhasin', 'url' => 'https://www.youtube.com/watch?v=sCBnF0DXqp8', 'style' => 'three', 'new_tab' => true],
                ]),
                'sort_order' => 2,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    private function seedMenus(): void
    {
        $schedule = '/event/india-law-ai-tech-summit-2025';
        $now = now();
        $add = function (string $location, string $label, ?string $url, int $sort, ?int $parent = null) use ($now) {
            return DB::table('menu_items')->insertGetId([
                'location' => $location,
                'parent_id' => $parent,
                'label' => $label,
                'url' => $url,
                'new_tab' => 0,
                'sort_order' => $sort,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        };

        $add('header', 'Home', '/', 1);
        $add('header', 'Speakers', '/speakers', 2);
        $add('header', 'Schedule', $schedule, 3);
        $add('header', 'Sponsors', '/#sponsors', 4);
        $add('header', 'Exhibitors', '/#exhibitors', 5);
        $gallery = $add('header', 'Gallery', '#', 6);
        $add('header', 'Images', '/gallery', 1, $gallery);
        $add('header', 'Videos', '/videos', 2, $gallery);
        $add('header', 'About', '/about', 7);

        $add('footer', 'Speakers', '/speakers', 1);
        $add('footer', 'Schedule', $schedule, 2);
        $add('footer', 'Sponsors', '/#sponsors', 3);
        $add('footer', 'Contact Us', '/contact', 4);
        $add('footer', 'Privacy Policy', '/privacy-policy', 5);
    }

    private function seedOrganizerTeam(): void
    {
        if (DB::table('teams')->where('year', 'Organizer')->exists()) {
            return;
        }
        $people = [
            ['Shreesh Chandra', 'Founder', 'shreesh.webp', 'https://www.linkedin.com/in/shreeshchandra/'],
            ['Vikesh Dhyani', 'Co-founder', 'vikesh.webp', 'https://www.linkedin.com/in/vikeshdhyani/'],
            ['Priyanka Srivastava', null, 'priyanka.webp', 'https://www.linkedin.com/in/priyanka-srivastava-31682794/'],
            ['Bhupendra Yadav', null, 'bhupendra.webp', 'https://www.linkedin.com/in/bhupendrayadav24/'],
            ['Mamchand Choudhary', null, 'mamchand.webp', 'https://www.linkedin.com/in/mamchand-choudhary-07310959/'],
        ];
        $now = now();
        foreach ($people as [$name, $position, $image, $linkedin]) {
            DB::table('teams')->insert([
                'name' => $name,
                'year' => 'Organizer',
                'position' => $position,
                'image' => $image,
                'bio' => null,
                'social' => json_encode(['facebook' => null, 'instagram' => null, 'x' => null, 'linkedin' => $linkedin, 'youtube' => null]),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
};
