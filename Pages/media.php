<?php
include '../navigation-bar/navbar.php';
include '../database/create_database.php'; // Database connection

$query = "SELECT * FROM gallery ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery | Maretlagadi Welfare Centre</title>

    <style>
        .gallery-hero {
            background-image: url('../assets/images/gallery-hero-bg.jpg');
            background-size: cover;
            background-position: center;
            min-height: 35vh;
            display: flex;
            align-items: center;
        }

        .gallery-overlay {
            width: 100%;
            background: rgba(0, 0, 0, 0.6);
            padding: 55px 0;
            color: #fff;
            text-align: center;
        }

        .gallery-overlay h1 {
            font-size: 2.4rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .gallery-item {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        }

        .gallery-item img {
            width: 100%;
            height: 230px;
            object-fit: cover;
            display: block;
            transition: transform 0.35s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.08);
        }

        .gallery-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.75), transparent);
            color: #fff;
            padding: 30px 14px 12px;
            font-size: 0.95rem;
            font-weight: 500;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .gallery-item:hover .gallery-caption {
            opacity: 1;
        }

        /* Lightbox */
        .lightbox-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.9);
            z-index: 1050;
            align-items: center;
            justify-content: center;
            padding: 30px;
        }

        .lightbox-overlay.active {
            display: flex;
        }

        .lightbox-overlay img {
            max-width: 90%;
            max-height: 80vh;
            border-radius: 8px;
        }

        .lightbox-close {
            position: absolute;
            top: 25px;
            right: 35px;
            color: #fff;
            font-size: 2rem;
            cursor: pointer;
            line-height: 1;
        }

        .lightbox-caption {
            position: absolute;
            bottom: 25px;
            left: 0;
            right: 0;
            text-align: center;
            color: #f1f1f1;
        }
    </style>
</head>

<body>

<section class="gallery-hero">
    <div class="gallery-overlay">
        <div class="container">
            <h1>Our Gallery</h1>
            <p>Moments from our programmes, events and community outreach.</p>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">

        <div class="row g-4">

            <?php if ($result && mysqli_num_rows($result) > 0): ?>

                <?php while ($row = mysqli_fetch_assoc($result)): ?>

                    <div class="col-md-4 col-sm-6">
                        <div class="gallery-item"
                             onclick="openLightbox('<?php echo htmlspecialchars($row['image_path'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['caption'] ?? '', ENT_QUOTES); ?>')">
                            <img src="<?php echo htmlspecialchars($row['image_path']); ?>"
                                 alt="<?php echo htmlspecialchars($row['caption'] ?? 'Gallery image'); ?>">
                            <?php if (!empty($row['caption'])): ?>
                                <div class="gallery-caption"><?php echo htmlspecialchars($row['caption']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <div class="col-12 text-center">
                    <p class="text-muted">No gallery images available yet. Please check back soon.</p>
                </div>

            <?php endif; ?>

        </div>

    </div>
</section>

<div class="lightbox-overlay" id="lightbox" onclick="closeLightbox()">
    <span class="lightbox-close">&times;</span>
    <img id="lightbox-img" src="" alt="">
    <div class="lightbox-caption" id="lightbox-caption"></div>
</div>

<script>
    function openLightbox(src, caption) {
        document.getElementById('lightbox-img').src = src;
        document.getElementById('lightbox-caption').textContent = caption;
        document.getElementById('lightbox').classList.add('active');
    }

    function closeLightbox() {
        document.getElementById('lightbox').classList.remove('active');
    }
</script>

<?php include '../footer/footer.php'; ?>

</body>
</html>