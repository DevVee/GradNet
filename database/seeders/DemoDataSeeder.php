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
        'Villanueva','Gomez','Dela Cruz','Ramos','Aquino','Navarro','Luna','Castillo',
        'Diaz','Hernandez','Magno','Pascual','Salazar','Ocampo','Espinosa','Ferrer',
        'Tolentino','Panlilio','Lacson','Mateo','Ngo','Macaraeg',
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
        "Just passed the board exam! 🎉 Four years of hard work finally paid off. Shoutout to my batchmates who kept me motivated. ICCBI forever! 💙",
        "Grateful for the education I received at ICCBI. It prepared me so well for the real world. Now three years into my career and loving every moment! 🙌",
        "Attended a seminar on healthcare management today. The lessons from our professors at ICCBI are still very relevant. Always learning, always growing. 📚",
        "Looking for fellow ICCBI graduates in the Batangas area interested in a casual get-together. Who's in? 🍻",
        "Happy to share that I just got promoted to Senior Nurse! Thank you to everyone who believed in me. This one is for all of us from Batch 2019. 🏥",
        "Throwback to our graduation day! Can't believe it's been 5 years already. Where has the time gone? Tag your batchmates! 🎓",
        "Just started my own tutoring center for nursing board exam reviewees. If you know anyone who needs help, send them my way! 📖",
        "Huge thanks to the ICCBI Alumni Association for the job fair last week. Found some really promising opportunities. This network is everything! 🤝",
        "Missing college life and our professors who pushed us to be our best. Wherever you are, thank you! ICCBI pride 💪",
        "Just completed an online certification in data analytics. Shoutout to my CS batchmates who inspired me to keep learning. 💻",
        "Reminder: alumni homecoming is coming up next month! Let's make it bigger than ever. See you all there! 📅",
        "Five years out of college and I still use the case studies from our ICCBI classes every day. 🌍",
        "To my fellow fresh grads looking for work — don't give up! It took me 3 months but I finally landed a job I love. Your time will come. 💼",
        "Had coffee with some batchmates this weekend. It's amazing how far we've all come. Proud of every single one of you! ☕",
        "Pro tip for new grads: your ICCBI alumni network is your biggest asset. Don't be shy to reach out. We're all here to help! 🌟",
        "Officially one year at my first job! 🎂 Thank you ICCBI for preparing me so well.",
        "Just got back from a medical mission in Taal. Proud to use my nursing skills to serve the community. ❤️",
        "Sharing my experience transitioning from employment to entrepreneurship. Happy to chat with anyone thinking about the same leap! 🚀",
        "ICCBI CS grads — any of you working in cybersecurity? Would love to connect. The field is booming! 🔐",
        "Grateful for this platform to reconnect with old friends. The ICCBI community is truly special! 🌸",
    ];

    public function run(): void
    {
        $this->command->info('🌱  Seeding demo data…');

        // ── Wipe previous demo data so re-runs are clean ──────────────
        $demoEmails = \App\Models\User::where('email', 'like', '%@demo.iccbi.edu.ph')
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
        $adminUser = \App\Models\User::where('email', 'admin@iccbi.edu.ph')->first();
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
        $admin = \App\Models\User::updateOrCreate(
            ['email' => 'admin@iccbi.edu.ph'],
            [
                'first_name'        => 'Admin',
                'last_name'         => 'ICCBI',
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'status'            => 'approved',
                'program'           => 'BSN',
                'graduation_year'   => 2019,   // ← matches batchmates below
                'employment_status' => 'Employed',
                'home_municipality' => 'Balayan',
                'profile_picture'   => 'https://i.pravatar.cc/200?img=70',
            ]
        );

        // ── 2. 25 alumni users ────────────────────────────────────────
        $this->command->info('   Creating alumni users…');
        $users = collect();

        // First 4 are BSN 2019 — same program+year as admin (batchmates)
        $batchmates = [
            // [first, last, program, year, employment, city, pravatar_img_n]
            ['Maria',   'Santos',   'BSN',   2019, 'Employed',     'Balayan', 1],
            ['Carlo',   'Reyes',    'BSN',   2019, 'Employed',     'Nasugbu', 12],
            ['Jasmine', 'Cruz',     'BSN',   2019, 'Student',      'Lemery',  2],
            ['Miguel',  'Bautista', 'BSN',   2019, 'Self-Employed','Tuy',     13],
        ];
        foreach ($batchmates as $b) {
            $email = strtolower($b[0] . '.' . $b[1] . '@demo.iccbi.edu.ph');
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
                'profile_picture'   => 'https://i.pravatar.cc/200?img=' . $b[6],
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
            $program   = $this->programs[($i + 1) % count($this->programs)];  // skip BSN for variety
            $gradYear  = 2017 + ($i % 7);
            $empStatus = $this->employmentStatuses[$i % count($this->employmentStatuses)];
            $muni      = $this->municipalities[$i % count($this->municipalities)];
            // pravatar.cc face photos (1–70) — reliable hotlink CDN
            // Even-indexed users use higher numbers to avoid duplicates with batchmates
            $portrait = 'https://i.pravatar.cc/200?img=' . (20 + $i);

            $email = strtolower($firstName . ($i + 4) . '@demo.iccbi.edu.ph');

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

        // Each user connects with 5–10 others
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
        // Admin is connected to EVERY demo user → lively feed
        foreach ($users as $u) {
            DB::table('connections')->insertOrIgnore([
                'follower_id' => $admin->id,
                'followed_id' => $u->id,
                'status'      => 'accepted',
                'created_at'  => now()->subDays(rand(1, 120)),
                'updated_at'  => now()->subDays(rand(0, 5)),
            ]);
        }

        // ── 4. Posts with images ──────────────────────────────────────
        $this->command->info('   Creating posts…');
        $allUsers = $users->push($admin);

        foreach ($allUsers as $idx => $u) {
            // 3–5 posts per user — ensures every feed looks full
            $numPosts = rand(3, 5);
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

                // ~70% of posts have an image — deterministic seed so images are stable
                if (($idx + $p) % 10 < 7) {
                    $imgSeed = (($idx * 7 + $p * 3) % 80) + 10; // seeds 10-89
                    DB::table('post_media')->insert([
                        'post_id'    => $postId,
                        'media_path' => 'https://picsum.photos/seed/alumni' . $imgSeed . '/800/500',
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
        $newsItems = [
            [
                'title'       => 'ICCBI Alumni Homecoming 2025 — A Night to Remember',
                'description' => "Last Saturday's alumni homecoming was an unforgettable celebration of friendship, growth, and the enduring ICCBI spirit. Over 500 graduates from various batches gathered at the school gymnasium, reconnecting with old friends and professors who shaped their careers.\n\nThe event featured a program highlighting alumni achievements, a tribute to retired faculty, and a showcase of how ICCBI graduates have made their mark across healthcare, business, technology, and public service.\n\n\"Seeing everyone come together after so many years is a reminder of why ICCBI is more than a school — it's a family,\" said Alumni Association President Ma. Teresa Flores (BSN 2015).",
                'img_seed'    => 20,
                'days_ago'    => 5,
            ],
            [
                'title'       => "ICCBI Partners with St. Patrick's Hospital for Nursing Internship Program",
                'description' => "ICCBI has signed a memorandum of agreement with St. Patrick's Hospital of Batangas to provide an expanded clinical internship program for nursing students, giving them access to a wider range of medical specializations.\n\nThe partnership will allow BSN students to rotate through departments including the ICU, emergency medicine, pediatrics, and oncology under the mentorship of experienced clinical supervisors, many of whom are proud ICCBI alumni.",
                'img_seed'    => 50,
                'days_ago'    => 10,
            ],
            [
                'title'       => 'Alumni Spotlight: From Graduate to Hospital Department Head',
                'description' => "This month we celebrate Rosario Diaz (BSN 2016), recently appointed Head of the Medical-Surgical Ward at a major Batangas City hospital — a remarkable achievement just eight years after graduation.\n\nRosario credits her success to the strong clinical foundation she built at ICCBI. \"My professors taught me not just the science of nursing but the compassion behind it,\" she shared.\n\nThe Alumni Association named Rosario the Outstanding Alumna for Health Sciences this year.",
                'img_seed'    => 64,
                'days_ago'    => 15,
            ],
            [
                'title'       => 'New Computer Lab Opens to Support Tech-Focused Curriculum',
                'description' => "ICCBI recently unveiled a state-of-the-art computer laboratory equipped with 60 high-performance workstations, funded in part by a generous donation from the Alumni Association's Tech Industry Chapter.\n\nThe laboratory features the latest development tools, simulation software, and a high-speed fiber internet connection, primarily serving Computer Science and Information Technology students.\n\nThe lab will also host workshops and seminars open to alumni looking to upskill in cloud computing, data science, and cybersecurity.",
                'img_seed'    => 96,
                'days_ago'    => 22,
            ],
            [
                'title'       => 'Annual Job Fair Connects 200+ Graduates with Top Employers',
                'description' => "The ICCBI Career Development Office and Alumni Association jointly hosted the annual job fair, drawing 40+ companies and over 200 graduating students and alumni.\n\nHighlights included on-site interviews from BDO Unibank, Batangas Medical Center, and Globe Telecom, with several conditional job offers extended on the day.\n\n\"We want our students to enter the workforce with confidence, and connecting them directly with employers who trust the ICCBI brand is how we do that,\" said Career Development Director Ramon Cruz.",
                'img_seed'    => 119,
                'days_ago'    => 30,
            ],
            [
                'title'       => 'ICCBI Nursing Graduates Achieve 95% Board Exam Passing Rate',
                'description' => "ICCBI's College of Nursing recorded an outstanding 95% first-time passing rate in the latest Nursing Licensure Examination, surpassing the national average and cementing ICCBI's reputation as one of the top nursing schools in CALABARZON.\n\nOf the 87 graduates who took the board exam, 83 passed on their first attempt. Three of the top ten national scorers from Region IV-A are ICCBI alumni.",
                'img_seed'    => 180,
                'days_ago'    => 40,
            ],
        ];

        foreach ($newsItems as $item) {
            DB::table('news')->insertOrIgnore([
                'title'       => $item['title'],
                'description' => $item['description'],
                'image_path'  => 'https://picsum.photos/seed/iccbi-news-' . $item['img_seed'] . '/1200/630',
                'uploaded_by' => $admin->id,
                'created_at'  => now()->subDays($item['days_ago']),
                'updated_at'  => now()->subDays($item['days_ago']),
            ]);
        }

        // ── 6. Events ─────────────────────────────────────────────────
        $this->command->info('   Creating events…');
        $eventItems = [
            [
                'title'       => 'ICCBI Alumni Homecoming & Grand Reunion 2025',
                'description' => "Join us for the grandest alumni gathering of the year! Reconnect with your batchmates, meet new faces, and celebrate the spirit of ICCBI together.\n\nThe evening features a program celebrating alumni milestones, tributes to retired faculty, cultural performances, a grand dinner buffet, and an after-party. Formal attire required.\n\nTickets available through the Alumni Association office. Early bird pricing ends two weeks before the event.",
                'location'    => 'ICCBI Gymnasium, Balayan, Batangas',
                'img_seed'    => 28,
                'days'        => 14,
            ],
            [
                'title'       => 'Healthcare Alumni Summit: Innovations in Patient Care',
                'description' => "A full-day professional development summit for ICCBI healthcare alumni. Theme: \"Technology-Driven Care: Preparing for the Future of Healthcare.\"\n\nSpeakers include department heads from leading Batangas hospitals, a telemedicine specialist, and a public health consultant from DOH.\n\nCPD units will be awarded. Free for ICCBI alumni. Light meals provided.",
                'location'    => 'Batangas City Convention Center',
                'img_seed'    => 42,
                'days'        => 21,
            ],
            [
                'title'       => 'Tech Talk: AI & Machine Learning for Filipino Developers',
                'description' => "Calling all CS and IT alumni! This half-day workshop covers practical AI and machine learning applications Filipino developers can use today.\n\nTopics: building AI-powered web apps, prompt engineering, integrating LLM APIs, and career opportunities in AI.\n\nBring your laptop. Hands-on exercises included. Certificates of attendance provided.",
                'location'    => 'ICCBI CS Building, Room 301',
                'img_seed'    => 60,
                'days'        => 7,
            ],
            [
                'title'       => 'Business & Entrepreneurship Forum for Alumni',
                'description' => "A morning of inspiration and practical knowledge for alumni who are entrepreneurs or aspiring to start their own businesses.\n\nHear from ICCBI alumni who built companies from the ground up, and get advice on funding, operations, and marketing. Open networking session after the program.",
                'location'    => 'Balayan Municipal Covered Court',
                'img_seed'    => 75,
                'days'        => 30,
            ],
            [
                'title'       => 'Medical Mission & Community Outreach — Taal, Batangas',
                'description' => "ICCBI Alumni Association invites all healthcare professionals among our graduates to join the bi-annual medical mission in Taal.\n\nServices: free consultations, blood pressure/blood sugar monitoring, dental check-ups, wound care, and medicine distribution. Goal: serve 300+ community members.\n\nVolunteers needed: nurses, dentists, pharmacists, and logistics helpers. Snacks and packed lunch provided.",
                'location'    => 'Taal Municipal Gymnasium, Taal, Batangas',
                'img_seed'    => 91,
                'days'        => 45,
            ],
            [
                'title'       => 'ICCBI Alumni vs. Faculty Basketball Cup 2025',
                'description' => "The annual Alumni vs. Faculty basketball showdown is back! Can the alumni team finally claim the championship trophy?\n\nOpen to all alumni. Team rosters finalized one week before. Cheerleading squads and supporters welcome. Food stalls open all day.\n\nAdmission free. Come show your ICCBI pride!",
                'location'    => 'ICCBI Sports Complex, Balayan',
                'img_seed'    => 110,
                'days'        => 60,
            ],
        ];

        foreach ($eventItems as $item) {
            DB::table('events')->insertOrIgnore([
                'title'          => $item['title'],
                'description'    => $item['description'],
                'image_path'     => 'https://picsum.photos/seed/iccbi-event-' . $item['img_seed'] . '/1200/630',
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
        $this->command->line('  Admin login  → <info>admin@iccbi.edu.ph</info>  /  <info>password</info>');
        $this->command->line('  Alumni login → any email like <info>Juan0@demo.iccbi.edu.ph</info>  /  <info>password</info>');
    }
}
