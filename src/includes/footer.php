<?php
session_start();
$base_url = "/~ptaw-2025-gr4";
?>
<footer>
    <div id="containerFooter">
        <div class="divFooter1">
            <h4>Print&Go</h4>
            <p>Custom printing solutions for everyone.</p>
        </div>
        <div class="divFooter">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="<?= $base_url ?>/index.php">Home</a></li>
                <li><a href="<?= $base_url ?>/src/produtos.php">Products</a></li>
                <li><a href="<?= $base_url ?>/src/sobre.php">About Us</a></li>
            </ul>
        </div>
        <div class="divFooter">
            <h4>Help</h4>
            <ul>
                <li><a href="<?= $base_url ?>/help-center.php#faq">FAQ</a></li>
                <li><a href="<?= $base_url ?>/help-center.php#terms">Terms of Service</a></li>
                <li><a href="<?= $base_url ?>/help-center.php#privacy">Privacy Policy</a></li>
                <li><a href="<?= $base_url ?>/help-center.php#shipping">Shipping Information</a></li>
                <li><a href="<?= $base_url ?>/help-center.php#returns">Returns</a></li>
                <li><a href="<?= $base_url ?>/help-center.php#size-guide">Size Guide</a></li>
            </ul>
        </div>
        <div class="divFooter">
            <h4>Follow Us</h4>
            <ul class="social">
                <li>
                    <a href="https://facebook.com" class="iconSocial"><img src="<?= $base_url ?>/imagens/fb.png"
                            alt="Facebook">
                    </a>
                </li>
                <li>
                    <a href="https://instagram.com" class="iconSocial"><img src="<?= $base_url ?>/imagens/ig.png"
                            alt="Instagram">
                    </a>
                </li>

            </ul>
        </div>
        <div class="divFooter">
            <a href="<?= $base_url ?>/contact.php"><h4>Contact Us</h4></a>
        </div>
    </div>

    <hr>

    <p class="direitos"> &#169; 2025 <b>Print&Go. </b>All rights reserved</p>

</footer>