<?php
/**
 * Erix Construction - Database Migration & Seeding Script
 * This script runs the SQL statements in schema.sql to create database and tables,
 * and then seeds the tables with default projects, blogs, and admin credentials.
 */

// Basic styling for the migration output
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erix Construction - Database Migration & Seeding</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #0b0b0b;
            color: #f5f0eb;
            font-family: "DM Sans", sans-serif;
            padding: 40px 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        h1 {
            color: #d4af37;
            font-weight: 500;
            border-bottom: 1px solid #2a2a2a;
            padding-bottom: 10px;
        }
        .step {
            background-color: #151515;
            border-left: 4px solid #d4af37;
            padding: 15px 20px;
            margin: 15px 0;
            border-radius: 0 8px 8px 0;
        }
        .success {
            color: #4caf50;
            font-weight: bold;
        }
        .error {
            color: #f44336;
            font-weight: bold;
        }
        .info {
            color: #2196f3;
        }
        pre {
            background-color: #050505;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
            color: #a0a0a0;
            font-size: 13px;
        }
        .btn-dashboard {
            display: inline-block;
            background-color: #d4af37;
            color: #0b0b0b;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            margin-top: 20px;
            transition: opacity 0.2s;
        }
        .btn-dashboard:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <h1>Erix Construction - Database Migration & Seeding</h1>';

// 1. Load DB Settings
$host = 'localhost';
$user = 'root';
$pass = ''; // Default XAMPP MySQL password is empty
$port = '3307';
$charset = 'utf8mb4';

try {
    echo '<div class="step">Connecting to MySQL server on port ' . htmlspecialchars($port) . '... ';
    $dsn = "mysql:host=$host;port=$port;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    echo '<span class="success">Connected!</span></div>';

    // 2. Read and Execute schema.sql
    $sqlPath = __DIR__ . '/../../schema.sql';
    echo '<div class="step">Reading SQL schema file from <code>' . htmlspecialchars($sqlPath) . '</code>... ';
    if (!file_exists($sqlPath)) {
        throw new Exception("schema.sql not found at " . $sqlPath);
    }
    $sql = file_get_contents($sqlPath);
    echo '<span class="success">Read ' . strlen($sql) . ' bytes.</span></div>';

    echo '<div class="step">Executing schema setup (creating database and tables)... ';
    $pdo->exec($sql);
    echo '<span class="success">Database and tables initialized successfully!</span></div>';

    // Select the database explicitly to make sure subsequent queries target it
    $pdo->exec("USE `erix_db`;");

    // 3. Seeding Admin User
    echo '<div class="step">Seeding admin users... ';
    $stmt = $pdo->query("SELECT COUNT(*) FROM admin_users");
    $adminCount = $stmt->fetchColumn();
    if ($adminCount == 0) {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $insertAdmin = $pdo->prepare("INSERT INTO admin_users (username, password, created_at) VALUES ('admin', ?, NOW())");
        $insertAdmin->execute([$hash]);
        echo '<span class="success">Default admin user created! (Username: admin, Password: admin123)</span>';
    } else {
        echo '<span class="info">Admin user already exists. Skipped.</span>';
    }
    echo '</div>';

    // 4. Seeding Projects
    echo '<div class="step">Seeding showcase projects... ';
    $stmt = $pdo->query("SELECT COUNT(*) FROM projects");
    $projectCount = $stmt->fetchColumn();
    if ($projectCount == 0) {
        $projectsData = [
            [
                "name" => "Skyline Residences",
                "category" => "Residential",
                "location" => "Mumbai, MH",
                "year" => "2024",
                "description" => "Skyline Residences stands as a monumental statement of architectural innovation and structural engineering excellence. Rising 24 storeys above the vibrant cityscape of Mumbai, this ultra-luxury residential tower offers 180 bespoke apartments designed to blend contemporary high-end aesthetics with premium functional sustainability.\n\nFrom the initial foundation engineering to the final interior fit-out, the construction team applied state-of-the-art building information modeling (BIM) to coordinate complex HVAC, electrical, and structural interfaces. The tower features a post-tensioned slab system that maximizes floor-to-ceiling heights and spans, allowing for spacious columns-free layouts inside all apartments.\n\nComplementing the structural engineering is an array of premium architectural highlights, including a double-height grand lobby featuring imported travertine stone, an infinity-edge swimming pool overlooking the Arabian Sea, and a comprehensive building management system that coordinates thermal comfort and solar load mitigation.",
                "floors" => "24",
                "units" => "180",
                "sq_ft" => "1.8M",
                "image_url" => "https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=1200&q=80"
            ],
            [
                "name" => "Nexus Corporate Park",
                "category" => "Commercial",
                "location" => "Pune, MH",
                "year" => "2023",
                "description" => "Nexus Corporate Park is a flagship Grade-A commercial development spanning three premium towers, designed to cater to modern corporate giants, co-working hubs, and upscale retail brands. Spanning a gross built-up area of 420,000 sq. ft., the project is engineered with an emphasis on energy efficiency, transit connectivity, and premium architectural presence.\n\nThe structural framework features post-tensioned wide-span beams and high-durability curtain wall glazing that reduces solar heat gains while maximizing natural daylight. Features include multi-level parking, integrated building management systems, high-speed destination-control elevators, and double-height lobbies dressed in custom-finished limestone and gold metal trim.",
                "floors" => "3 Towers",
                "units" => "14M Value",
                "sq_ft" => "420K",
                "image_url" => "https://images.unsplash.com/photo-1497366216548-37526070297c?w=1200&q=80"
            ],
            [
                "name" => "The Amber Penthouse",
                "category" => "Interior",
                "location" => "Bangalore, KA",
                "year" => "2023",
                "description" => "The Amber Penthouse is a premier interior fit-out and architectural redesign of a 6,000 sq ft luxury duplex residence. Centred on custom luxury and organic minimalism, this project showcases bespoke joinery, imported Italian marble floorings, and a sophisticated lighting design scheme integrated with home automation.\n\nOur interior design and construction divisions collaborated closely to source premium materials and execute complex spatial reorganizations, including installing a suspended floating steel-and-hardwood staircase and executing double-height floor-to-ceiling glass panel windows that frame views of the city skyline.",
                "floors" => "9 Months",
                "units" => "5★ Rated",
                "sq_ft" => "6K",
                "image_url" => "https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=1200&q=80"
            ],
            [
                "name" => "Greenfield Villas",
                "category" => "Residential",
                "location" => "Hyderabad, TS",
                "year" => "2022",
                "description" => "Greenfield Villas is an elite gated residential development comprising 40 ultra-luxury private villas nested in a beautifully landscaped 12-acre campus. Every villa is individually configured with private courtyards, swimming pools, double-height family lounges, and top-tier home automation systems.\n\nThe development features sustainable low-impact civil engineering practices, rainwater harvesting networks, decentralized solar grids, and a central 20,000 sq. ft. club house with state-of-the-art sports facilities and dining halls.",
                "floors" => "40 Villas",
                "units" => "22M Value",
                "sq_ft" => "12 Acres",
                "image_url" => "https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=1200&q=80"
            ],
            [
                "name" => "Heritage Grand Hotel",
                "category" => "Renovation",
                "location" => "Delhi, DL",
                "year" => "2022",
                "description" => "The Heritage Grand Hotel project represents a highly specialized, comprehensive structural rehabilitation and interior retrofitting of a landmark colonial-era building constructed in 1920. The challenge was to integrate contemporary building service systems (HVAC, fire safety, and plumbing) while strictly restoring the heritage facade and woodwork.\n\nOur restoration division executed careful structural underpinning, concrete jacketing, and brickwork rehabilitation, working alongside local craftsmen to replicate original plaster details and wood moldings. The result is a seamless combination of 100-year-old character with modern 5-star hotel luxury expectations.",
                "floors" => "1920 Built",
                "units" => "2Yr Timeline",
                "sq_ft" => "120 Rooms",
                "image_url" => "https://images.unsplash.com/photo-1572120360610-d971b9d7767c?w=1200&q=80"
            ],
            [
                "name" => "Orion Mall Extension",
                "category" => "Commercial",
                "location" => "Chennai, TN",
                "year" => "2021",
                "description" => "The Orion Mall Extension is a massive 90,000 sq ft retail expansion built adjacent to an active shopping complex. The development includes a 12-screen cinema multiplex, a grand food court area, and space for over 30 retail brands.\n\nTo ensure zero business disruption for the existing mall, the construction was executed using highly coordinated off-site precast framing and composite deck systems, allowing rapid structural erection during night shifts. The project features clean modern facades, high-capacity central cooling systems, and double-height entrance arches.",
                "floors" => "12 Screens",
                "units" => "30+ Outlets",
                "sq_ft" => "90K",
                "image_url" => "https://images.unsplash.com/photo-1565538810643-b5bdb714032a?w=1200&q=80"
            ]
        ];

        $insertProj = $pdo->prepare("INSERT INTO projects (name, category, location, year, description, floors, units, sq_ft, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($projectsData as $p) {
            $insertProj->execute([
                $p['name'],
                $p['category'],
                $p['location'],
                $p['year'],
                $p['description'],
                $p['floors'],
                $p['units'],
                $p['sq_ft'],
                $p['image_url']
            ]);
        }
        echo '<span class="success">Seeded 6 projects!</span>';
    } else {
        echo '<span class="info">Projects already seeded. Skipped.</span>';
    }
    echo '</div>';

    // 5. Seeding Blogs
    echo '<div class="step">Seeding blog posts... ';
    $stmt = $pdo->query("SELECT COUNT(*) FROM blogs");
    $blogCount = $stmt->fetchColumn();
    if ($blogCount == 0) {
        $blogsData = [
            [
                "title" => "Modern Trends in Sustainable Residential Construction",
                "author" => "Aditya Aman",
                "date_published" => "2026-06-05",
                "content" => "Sustainable construction is no longer a niche market choice; it has evolved into the definitive gold standard for luxury residential developments worldwide. Modern buyers demand homes that not only provide absolute comfort but also feature a minimal carbon footprint.\n\nAt Erix Construction, we are implementing green concrete technologies—including carbon-curing and industrial fly-ash replacements—that cut carbon emissions associated with structural concrete by up to 40%. Combining this with high-performance home-scale solar grids and passive solar design principles keeps indoor spaces naturally comfortable year-round, dramatically reducing active mechanical cooling loads.\n\nFrom smart insulation systems to graywater recycling infrastructure, residential landmarks are evolving to act as self-contained sustainable ecosystems.",
                "image_url" => "https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=600&q=80",
                "summary" => "How green concrete, carbon-curing, home-scale solar grids, and passive ventilation systems are reshaping luxury villa developments in 2025."
            ],
            [
                "title" => "Navigating Complex Logistics in Dense Urban Sites",
                "author" => "Vikram Malhotra",
                "date_published" => "2026-05-28",
                "content" => "Building high-rise structures in dense metropolitan districts presents a distinct set of coordination challenges. With highly congested traffic corridors, strict noise limits, and absolutely no staging space on site, logistics management becomes the difference between a successful project and a scheduling nightmare.\n\nTo overcome these urban constraints, Erix employs real-time Just-in-Time (JIT) material dispatching networks. Large structural items, concrete mixers, and glazing panels are tracked via GPS and coordinated to arrive precisely when the tower cranes are ready to hoist them. Scheduling large concreting tasks during night operation windows allows us to utilize empty city lanes, ensuring a safe, rapid, and uninterrupted supply flow.",
                "image_url" => "https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=600&q=80",
                "summary" => "An in-depth look at implementing real-time Just-in-Time materials dispatching and night windows to avoid metro traffic congestion during heavy structural pours."
            ],
            [
                "title" => "The Evolution of Villa Architecture & Modern Design",
                "author" => "Priya Sharma",
                "date_published" => "2026-05-15",
                "content" => "Villa architecture has undergone a radical transformation over the past decade. The traditional design language of symmetrical classical columns and heavy partition walls is giving way to open-plan layouts, structural organic cantilevers, and seamless indoor-outdoor transitions.\n\nModern residential luxury focuses on natural light, material authenticity, and fluid spatial geometry. By utilizing high-strength steel framing and post-tensioned concrete, we can support massive cantilevering roofs and wide openings without internal column supports. Glazed sliding panels merge double-height lounges with landscaped gardens and decks, making nature a central element of the interior experience.",
                "image_url" => "https://images.unsplash.com/photo-1600585154526-990dced4db0d?w=600&q=80",
                "summary" => "Exploring the shifting paradigms of high-end private residences—moving from rigid symmetrical columns to organic cantilevers, massive sliding glass panels, and open spaces."
            ]
        ];

        $insertBlog = $pdo->prepare("INSERT INTO blogs (title, author, date_published, content, image_url, summary) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($blogsData as $b) {
            $insertBlog->execute([
                $b['title'],
                $b['author'],
                $b['date_published'],
                $b['content'],
                $b['image_url'],
                $b['summary']
            ]);
        }
        echo '<span class="success">Seeded 3 blog articles!</span>';
    } else {
        echo '<span class="info">Blogs already seeded. Skipped.</span>';
    }
    echo '</div>';

    echo '<div style="margin-top: 30px; border-top: 1px solid #2a2a2a; padding-top: 20px;">';
    echo '<span class="success" style="font-size: 18px;">Migration & Seeding Completed Successfully!</span><br>';
    echo '<a href="index.php" class="btn-dashboard">Go to Admin Dashboard</a>';
    echo '</div>';

} catch (Exception $e) {
    echo '<div class="step"><span class="error">Migration failed!</span><br>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre></div>';
}

echo '</body>
</html>';
?>
