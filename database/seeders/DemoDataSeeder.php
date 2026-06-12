<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    // ── Data pools ────────────────────────────────────────────────────
    private array $firstNamesMale = [
        'Juan','Carlo','Miguel','Rafael','Jose','Angelo','Christian','Mark','Paolo',
        'Gabriel','Luis','Renzo','Daniel','Andrei','Patrick','Francis','Ryan','Sean',
        'Kenneth','Nico','Jayson','Rommel','Alvin','Dennis','Rodel','Aldrin',
    ];
    private array $firstNamesFemale = [
        'Maria','Ana','Jasmine','Nicole','Kristine','Angelica','Michelle','Christine',
        'Trisha','Camille','Bianca','Liza','Rhea','Patricia','Hazel','Jeannie','Mia',
        'Rose','Hannah','Clarissa','Sophia','Dana','Abigail','Precious','Mariz','Genelyn',
    ];
    private array $lastNames = [
        'Santos','Reyes','Cruz','Bautista','Garcia','Mendoza','Torres','Flores',
        'Villanueva','Gomez','Ramos','Aquino','Navarro','Luna','Castillo',
        'Diaz','Hernandez','Magno','Pascual','Salazar','Ocampo','Espinosa','Ferrer',
        'Tolentino','Panlilio','Lacson','Mateo','Ngo','Macaraeg','Soriano',
    ];
    private array $programs = [
        'BSN', 'BSCS', 'BSBA', 'BSEd', 'BSCrim', 'BSA', 'BSEngr', 'BSPsych',
    ];
    private array $municipalities = [
        'Balayan','Nasugbu','Lemery','Tuy','Calatagan','Lian','Calaca','Taal',
        'San Luis','Bauan','Mabini','San Jose','Padre Garcia','Rosario','Lobo',
    ];
    private array $companies = [
        "St. Patrick's Hospital", 'PhilHealth Regional Office', 'SM Prime Holdings',
        'BDO Unibank', 'Philippine National Police', 'DepEd Batangas',
        'Batangas Medical Center', 'Jollibee Foods Corporation',
        'Manila Electric Company', 'Land Bank of the Philippines',
        'Accenture Philippines', 'Globe Telecom',
    ];
    private array $employmentStatuses = [
        'Employed','Employed','Employed','Self-Employed','Student','Unemployed',
    ];
    private array $postContents = [
        "Just passed the board exam! 🎉 Four years of hard work finally paid off. Shoutout to my batchmates who kept me motivated. GradNet forever! 💙",
        "Grateful for the education I received. It prepared me so well for the real world. Now three years into my career and loving every moment! 🙌",
        "Attended a seminar on professional development today. The lessons from our professors are still very relevant. Always learning, always growing. 📚",
        "Looking for fellow graduates in the Batangas area interested in a casual get-together. Who's in? 🍻",
        "Happy to share that I just got promoted to a senior role! Thank you to everyone who believed in me. 🏥",
        "Throwback to our graduation day! Can't believe it's been 5 years already. Where has the time gone? Tag your batchmates! 🎓",
        "Just started my own business helping board exam reviewees. If you know anyone who needs help, send them my way! 📖",
        "Huge thanks to the GradNet Alumni Association for the job fair last week. Found some really promising opportunities. This network is everything! 🤝",
        "Missing college life and our professors who pushed us to be our best. Wherever you are, thank you! Alumni pride 💪",
        "Just completed an online certification in data analytics. Shoutout to my CS batchmates who inspired me to keep learning. 💻",
        "Reminder: alumni homecoming is coming up next month! Let's make it bigger than ever. See you all there! 📅",
        "Five years out of college and I still use the case studies from our classes every day. 🌍",
        "To my fellow fresh grads looking for work — don't give up! It took me 3 months but I finally landed a job I love. Your time will come. 💼",
        "Had coffee with some batchmates this weekend. It's amazing how far we've all come. Proud of every single one of you! ☕",
        "Pro tip for new grads: your alumni network is your biggest asset. Don't be shy to reach out. We're all here to help! 🌟",
        "Officially one year at my first job! 🎂 Thank you for preparing me so well.",
        "Just got back from a community outreach in Taal. Proud to use my skills to serve people. ❤️",
        "Sharing my experience transitioning from employment to entrepreneurship. Happy to chat with anyone thinking about the same leap! 🚀",
        "Any fellow graduates working in tech or cybersecurity? Would love to connect. The field is booming! 🔐",
        "Grateful for this platform to reconnect with old friends. The GradNet community is truly special! 🌸",
    ];

    public function run(): void
    {
        $this->command->info('🌱  Seeding demo data…');

        // ── Wipe previous demo data so re-runs are clean ──────────────
        $demoEmails = \App\Models\User::where('email', 'like', '%@demo.gradnet.ph')
            ->orWhere('email', 'like', '%@demo.iccbi.edu.ph')
            ->pluck('id');
        if ($demoEmails->isNotEmpty()) {
            DB::table('connections')->where(function ($q) use ($demoEmails) {
                $q->whereIn('follower_id', $demoEmails)
                  ->orWhereIn('followed_id', $demoEmails);
            })->delete();
            $postIds = DB::table('posts')->whereIn('user_id', $demoEmails)->pluck('id');
            DB::table('post_reactions')->whereIn('post_id', $postIds)->delete();
            DB::table('post_media')->whereIn('post_id', $postIds)->delete();
            DB::table('posts')->whereIn('user_id', $demoEmails)->delete();
            \App\Models\User::whereIn('id', $demoEmails)->delete();
        }
        // Also wipe admin's demo connections/posts so re-seed is fresh
        $adminUser = \App\Models\User::where('email', 'admin@gradnet.ph')
            ->orWhere('email', 'admin@iccbi.edu.ph')->first();
        if ($adminUser) {
            DB::table('connections')->where('follower_id', $adminUser->id)
                ->orWhere('followed_id', $adminUser->id)->delete();
            $aPostIds = DB::table('posts')->where('user_id', $adminUser->id)->pluck('id');
            DB::table('post_reactions')->whereIn('post_id', $aPostIds)->delete();
            DB::table('post_media')->whereIn('post_id', $aPostIds)->delete();
            DB::table('posts')->where('user_id', $adminUser->id)->delete();
        }
        DB::table('news')->truncate();
        DB::table('events')->truncate();

        // ── 1. Admin user ─────────────────────────────────────────────
        // Migrate old email if it exists
        \App\Models\User::where('email', 'admin@iccbi.edu.ph')
            ->update(['email' => 'admin@gradnet.ph']);

        $admin = \App\Models\User::updateOrCreate(
            ['email' => 'admin@gradnet.ph'],
            [
                'first_name'        => 'Prince Arvee',
                'last_name'         => 'Avena',
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'status'            => 'approved',
                'program'           => 'BSCS',
                'graduation_year'   => 2022,
                'employment_status' => 'Employed',
                'home_municipality' => 'Balayan',
                'profile_picture'   => 'prince-arvee.jpg',
            ]
        );

        // ── 2. 25 alumni users ────────────────────────────────────────
        $this->command->info('   Creating alumni users…');
        $users = collect();

        // 7 named BSCS 2022 batchmates — same program+year as admin (show on dashboard)
        // first, last, program, grad_year, employment, municipality, photo_file
        $batchmates = [
            ['Kian',         'Bahia',   'BSCS', 2022, 'Employed',     'Balayan',  'kian-bahia.png'],
            ['Christian',    'Avena',   'BSCS', 2022, 'Employed',     'Nasugbu',  'christian-avena.png'],
            ['Errol',        'Alday',   'BSCS', 2022, 'Self-Employed','Lemery',   'errol-alday.png'],
            ['John Rafael',  'Marata',  'BSCS', 2022, 'Employed',     'Tuy',      'john-marata.png'],
            ['Ken',          'Estillo', 'BSCS', 2022, 'Employed',     'Calaca',   'ken-estillo.png'],
            ['Chito',        'Afable',  'BSCS', 2022, 'Student',      'Lian',     'chito-afable.png'],
            ['Ralph',        'Cabello', 'BSCS', 2022, 'Employed',     'Tuy',      'ralph-cabello.png'],
        ];
        foreach ($batchmates as $b) {
            $email = strtolower(str_replace(' ', '', $b[0]) . '.' . strtolower($b[1]) . '@demo.gradnet.ph');
            $user  = \App\Models\User::create([
                'first_name'        => $b[0],
                'last_name'         => $b[1],
                'email'             => $email,
                'password'          => Hash::make('password'),
                'role'              => 'user',
                'status'            => 'approved',
                'program'           => $b[2],
                'graduation_year'   => $b[3],
                'employment_status' => $b[4],
                'home_municipality' => $b[5],
                'profile_picture'   => $b[6],
            ]);
            $users->push($user);
        }

        // Remaining 21 varied alumni
        for ($i = 0; $i < 21; $i++) {
            $isMale    = $i % 2 === 0;
            $firstName = $isMale
                ? $this->firstNamesMale[$i % count($this->firstNamesMale)]
                : $this->firstNamesFemale[$i % count($this->firstNamesFemale)];
            $lastName  = $this->lastNames[($i + 4) % count($this->lastNames)];
            $program   = $this->programs[($i + 1) % count($this->programs)];
            $gradYear  = 2017 + ($i % 7);
            $empStatus = $this->employmentStatuses[$i % count($this->employmentStatuses)];
            $muni      = $this->municipalities[$i % count($this->municipalities)];
            $portrait  = 'https://i.pravatar.cc/200?img=' . (20 + $i);

            $email = strtolower($firstName . ($i + 4) . '@demo.gradnet.ph');

            $user = \App\Models\User::create([
                'first_name'         => $firstName,
                'last_name'          => $lastName,
                'email'              => $email,
                'password'           => Hash::make('password'),
                'role'               => 'user',
                'status'             => 'approved',
                'program'            => $program,
                'graduation_year'    => $gradYear,
                'employment_status'  => $empStatus,
                'home_municipality'  => $muni,
                'present_occupation' => $empStatus === 'Employed'
                    ? $this->companies[$i % count($this->companies)]
                    : null,
                'profile_picture'    => $portrait,
            ]);
            $users->push($user);
        }

        // ── 3. Connections ────────────────────────────────────────────
        $this->command->info('   Creating connections…');
        $allIds = $users->pluck('id')->toArray();

        foreach ($users as $u) {
            $targets = collect($allIds)
                ->filter(fn($id) => $id !== $u->id)
                ->shuffle()->take(rand(5, 10));
            foreach ($targets as $tid) {
                DB::table('connections')->insertOrIgnore([
                    'follower_id' => $u->id,
                    'followed_id' => $tid,
                    'status'      => 'accepted',
                    'created_at'  => now()->subDays(rand(5, 180)),
                    'updated_at'  => now()->subDays(rand(0, 30)),
                ]);
            }
        }
        // Admin is connected to EVERY demo user (bidirectional) → lively feed
        foreach ($users as $u) {
            // Admin follows user
            DB::table('connections')->insertOrIgnore([
                'follower_id' => $admin->id,
                'followed_id' => $u->id,
                'status'      => 'accepted',
                'created_at'  => now()->subDays(rand(1, 120)),
                'updated_at'  => now()->subDays(rand(0, 5)),
            ]);
            // User follows admin back
            DB::table('connections')->insertOrIgnore([
                'follower_id' => $u->id,
                'followed_id' => $admin->id,
                'status'      => 'accepted',
                'created_at'  => now()->subDays(rand(1, 110)),
                'updated_at'  => now()->subDays(rand(0, 5)),
            ]);
        }

        // ── 4. Posts with images ──────────────────────────────────────
        $this->command->info('   Creating posts…');
        $allUsers = $users->push($admin);

        foreach ($allUsers as $idx => $u) {
            $numPosts = rand(6, 9);
            for ($p = 0; $p < $numPosts; $p++) {
                $content = $this->postContents[($idx * 4 + $p) % count($this->postContents)];
                $daysAgo = rand(0, 90);

                $postId = DB::table('posts')->insertGetId([
                    'user_id'    => $u->id,
                    'content'    => $content,
                    'is_public'  => rand(0, 9) > 1 ? 1 : 0,
                    'created_at' => now()->subDays($daysAgo)->subHours(rand(0, 23)),
                    'updated_at' => now()->subDays($daysAgo),
                ]);

                // ~70% of posts have an image — pick from a curated pool of social/lifestyle photos
                if (($idx + $p) % 10 < 7) {
                    // Unsplash photos that look like realistic social posts (people, campus, celebrations)
                    $postImages = [
                        'https://images.unsplash.com/photo-1543269664-76bc3997d9ea?w=800&h=500&fit=crop&auto=format', // happy graduates group
                        'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?w=800&h=500&fit=crop&auto=format', // friends group
                        'https://images.unsplash.com/photo-1515187029135-18ee286d815b?w=800&h=500&fit=crop&auto=format', // colleagues
                        'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&h=500&fit=crop&auto=format', // study group
                        'https://images.unsplash.com/photo-1517048676732-d65bc937f952?w=800&h=500&fit=crop&auto=format', // meeting
                        'https://images.unsplash.com/photo-1543269664-76bc3997d9ea?w=800&h=500&fit=crop&auto=format', // happy graduates
                        'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=800&h=500&fit=crop&auto=format', // hospital/medical
                        'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=800&h=500&fit=crop&auto=format', // coding
                        'https://images.unsplash.com/photo-1527529482837-4698179dc6ce?w=800&h=500&fit=crop&auto=format', // celebration
                        'https://images.unsplash.com/photo-1551836022-deb4988cc6c0?w=800&h=500&fit=crop&auto=format', // job fair
                        'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=800&h=500&fit=crop&auto=format', // hospital
                        'https://images.unsplash.com/photo-1574158622682-e40e69881006?w=800&h=500&fit=crop&auto=format', // coffee friends
                    ];
                    $imgIdx = (($idx * 7 + $p * 3) % count($postImages));
                    DB::table('post_media')->insert([
                        'post_id'    => $postId,
                        'media_path' => $postImages[$imgIdx],
                        'media_type' => 'image',
                        'created_at' => now()->subDays($daysAgo),
                        'updated_at' => now()->subDays($daysAgo),
                    ]);
                }

                // Random reactions (0-10 loves)
                $reactorPool = $allUsers->shuffle()->take(rand(0, 10));
                foreach ($reactorPool as $reactor) {
                    if ($reactor->id === $u->id) continue;
                    DB::table('post_reactions')->insertOrIgnore([
                        'post_id'       => $postId,
                        'user_id'       => $reactor->id,
                        'reaction_type' => 'love',
                        'created_at'    => now()->subDays(max(0, $daysAgo - 1)),
                    ]);
                }
            }
        }

        // ── 5. News articles ──────────────────────────────────────────
        $this->command->info('   Creating news articles…');
        // Unsplash direct photo IDs — stable, topically accurate images
        // Format: https://images.unsplash.com/photo-{ID}?w=1200&h=630&fit=crop&auto=format
        $newsItems = [
            [
                'title'       => 'GradNet Alumni Homecoming 2025 — A Night to Remember',
                'description' => "Last Saturday's alumni homecoming was an unforgettable celebration of friendship, growth, and the enduring GradNet spirit. Over 500 graduates from various batches gathered at the school gymnasium, reconnecting with old friends and professors who shaped their careers.\n\nThe event featured a program highlighting alumni achievements, a tribute to retired faculty, and a showcase of how GradNet graduates have made their mark across healthcare, business, technology, and public service.\n\n\"Seeing everyone come together after so many years is a reminder of why this institution is more than a school — it's a family,\" said Alumni Association President Ma. Teresa Flores (BSN 2015).",
                // Alumni celebration / homecoming party
                'image'       => 'https://images.unsplash.com/photo-1527529482837-4698179dc6ce?w=1200&h=630&fit=crop&auto=format',
                'days_ago'    => 5,
            ],
            [
                'title'       => "GradNet Partners with Leading Hospitals for Nursing Internship Program",
                'description' => "GradNet has signed memoranda of agreement with several leading hospitals in Batangas to provide an expanded clinical internship program for nursing students, giving them access to a wider range of medical specializations.\n\nThe partnerships will allow BSN students to rotate through departments including the ICU, emergency medicine, pediatrics, and oncology under the mentorship of experienced clinical supervisors, many of whom are proud GradNet alumni.",
                // Hospital corridor
                'image'       => 'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=1200&h=630&fit=crop&auto=format',
                'days_ago'    => 10,
            ],
            [
                'title'       => 'Alumni Spotlight: From Graduate to Hospital Department Head',
                'description' => "This month we celebrate Rosario Salazar (BSN 2016), recently appointed Head of the Medical-Surgical Ward at a major Batangas City hospital — a remarkable achievement just eight years after graduation.\n\nRosario credits her success to the strong clinical foundation she built during her studies. \"My professors taught me not just the science of nursing but the compassion behind it,\" she shared.\n\nThe GradNet Alumni Association named Rosario the Outstanding Alumna for Health Sciences this year.",
                // Nurse / medical professional
                'image'       => 'https://images.unsplash.com/photo-1584820927498-cfe5211fd8bf?w=1200&h=630&fit=crop&auto=format',
                'days_ago'    => 15,
            ],
            [
                'title'       => 'New Technology Lab Opens to Support Modern Curriculum',
                'description' => "The institution recently unveiled a state-of-the-art computer laboratory equipped with 60 high-performance workstations, funded in part by a generous donation from the GradNet Alumni Association's Tech Industry Chapter.\n\nThe laboratory features the latest development tools, simulation software, and a high-speed fiber internet connection, primarily serving Computer Science and IT students.\n\nThe lab will also host workshops and seminars open to alumni looking to upskill in cloud computing, data science, and cybersecurity.",
                // Computer lab / technology
                'image'       => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=1200&h=630&fit=crop&auto=format',
                'days_ago'    => 22,
            ],
            [
                'title'       => 'Annual Job Fair Connects 200+ Graduates with Top Employers',
                'description' => "The GradNet Career Development Office and Alumni Association jointly hosted the annual job fair, drawing 40+ companies and over 200 graduating students and alumni.\n\nHighlights included on-site interviews from BDO Unibank, Batangas Medical Center, and Globe Telecom, with several conditional job offers extended on the day.\n\n\"We want our students to enter the workforce with confidence, and connecting them directly with employers who trust the GradNet brand is how we do that,\" said Career Development Director Ramon Cruz.",
                // Career fair / people at booths
                'image'       => 'https://images.unsplash.com/photo-1551836022-deb4988cc6c0?w=1200&h=630&fit=crop&auto=format',
                'days_ago'    => 30,
            ],
            [
                'title'       => 'GradNet Nursing Graduates Achieve 95% Board Exam Passing Rate',
                'description' => "GradNet's College of Nursing recorded an outstanding 95% first-time passing rate in the latest Nursing Licensure Examination, surpassing the national average and cementing GradNet's reputation as one of the top nursing schools in CALABARZON.\n\nOf the 87 graduates who took the board exam, 83 passed on their first attempt. Three of the top ten national scorers from Region IV-A are GradNet alumni.",
                // Students studying / exam
                'image'       => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=1200&h=630&fit=crop&auto=format',
                'days_ago'    => 40,
            ],
        ];

        foreach ($newsItems as $item) {
            DB::table('news')->insertOrIgnore([
                'title'       => $item['title'],
                'description' => $item['description'],
                'image_path'  => $item['image'],
                'uploaded_by' => $admin->id,
                'created_at'  => now()->subDays($item['days_ago']),
                'updated_at'  => now()->subDays($item['days_ago']),
            ]);
        }

        // ── 6. Events ─────────────────────────────────────────────────
        $this->command->info('   Creating events…');
        $eventItems = [
            [
                'title'       => 'GradNet Alumni Homecoming & Grand Reunion 2025',
                'description' => "Join us for the grandest alumni gathering of the year! Reconnect with your batchmates, meet new faces, and celebrate the spirit of GradNet together.\n\nThe evening features a program celebrating alumni milestones, tributes to retired faculty, cultural performances, a grand dinner buffet, and an after-party. Formal attire required.\n\nTickets available through the Alumni Association office. Early bird pricing ends two weeks before the event.",
                'location'    => 'School Gymnasium, Balayan, Batangas',
                // Friends / alumni gathering celebration
                'image'       => 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?w=1200&h=630&fit=crop&auto=format',
                'days'        => 14,
            ],
            [
                'title'       => 'Healthcare Alumni Summit: Innovations in Patient Care',
                'description' => "A full-day professional development summit for GradNet healthcare alumni. Theme: \"Technology-Driven Care: Preparing for the Future of Healthcare.\"\n\nSpeakers include department heads from leading Batangas hospitals, a telemedicine specialist, and a public health consultant from DOH.\n\nCPD units will be awarded. Free for GradNet alumni. Light meals provided.",
                'location'    => 'Batangas City Convention Center',
                // Medical / healthcare conference
                'image'       => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=1200&h=630&fit=crop&auto=format',
                'days'        => 21,
            ],
            [
                'title'       => 'Tech Talk: AI & Machine Learning for Filipino Developers',
                'description' => "Calling all CS and IT alumni! This half-day workshop covers practical AI and machine learning applications Filipino developers can use today.\n\nTopics: building AI-powered web apps, prompt engineering, integrating LLM APIs, and career opportunities in AI.\n\nBring your laptop. Hands-on exercises included. Certificates of attendance provided.",
                'location'    => 'CS Building, Room 301',
                // Coding / tech talk / laptop workshop
                'image'       => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=1200&h=630&fit=crop&auto=format',
                'days'        => 7,
            ],
            [
                'title'       => 'Business & Entrepreneurship Forum for Alumni',
                'description' => "A morning of inspiration and practical knowledge for alumni who are entrepreneurs or aspiring to start their own businesses.\n\nHear from GradNet alumni who built companies from the ground up, and get advice on funding, operations, and marketing. Open networking session after the program.",
                'location'    => 'Balayan Municipal Covered Court',
                // Business meeting / conference room
                'image'       => 'https://images.unsplash.com/photo-1431540015161-0bf868a2d407?w=1200&h=630&fit=crop&auto=format',
                'days'        => 30,
            ],
            [
                'title'       => 'Medical Mission & Community Outreach — Taal, Batangas',
                'description' => "GradNet Alumni Association invites all healthcare professionals among our graduates to join the bi-annual medical mission in Taal.\n\nServices: free consultations, blood pressure/blood sugar monitoring, dental check-ups, wound care, and medicine distribution. Goal: serve 300+ community members.\n\nVolunteers needed: nurses, dentists, pharmacists, and logistics helpers. Snacks and packed lunch provided.",
                'location'    => 'Taal Municipal Gymnasium, Taal, Batangas',
                // Community health / doctors serving people
                'image'       => 'https://images.unsplash.com/photo-1584515933487-779824d29309?w=1200&h=630&fit=crop&auto=format',
                'days'        => 45,
            ],
            [
                'title'       => 'GradNet Alumni vs. Faculty Basketball Cup 2025',
                'description' => "The annual Alumni vs. Faculty basketball showdown is back! Can the alumni team finally claim the championship trophy?\n\nOpen to all alumni. Team rosters finalized one week before. Cheerleading squads and supporters welcome. Food stalls open all day.\n\nAdmission free. Come show your GradNet pride!",
                'location'    => 'School Sports Complex, Balayan',
                // Basketball game
                'image'       => 'https://images.unsplash.com/photo-1546519638-68e109498ffc?w=1200&h=630&fit=crop&auto=format',
                'days'        => 60,
            ],
        ];

        foreach ($eventItems as $item) {
            DB::table('events')->insertOrIgnore([
                'title'          => $item['title'],
                'description'    => $item['description'],
                'image_path'     => $item['image'],
                'event_datetime' => now()->addDays($item['days'])->setHour(rand(9, 17))->setMinute(0),
                'location'       => $item['location'],
                'uploaded_by'    => $admin->id,
                'created_at'     => now()->subDays(rand(1, 7)),
                'updated_at'     => now()->subDays(rand(0, 3)),
            ]);
        }

        // ── Summary ───────────────────────────────────────────────────
        $this->command->info('');
        $this->command->info('✅  Done!');
        $this->command->table(['Type', 'Count'], [
            ['Users',       $users->count() + 1],
            ['Connections', DB::table('connections')->count()],
            ['Posts',       DB::table('posts')->count()],
            ['Post Media',  DB::table('post_media')->count()],
            ['Reactions',   DB::table('post_reactions')->count()],
            ['News',        DB::table('news')->count()],
            ['Events',      DB::table('events')->count()],
        ]);
        $this->command->info('');
        $this->command->line('  Admin login  → <info>admin@gradnet.ph</info>  /  <info>password</info>');
        $this->command->line('  Alumni login → any email like <info>Maria.Santos@demo.gradnet.ph</info>  /  <info>password</info>');
    }
}
