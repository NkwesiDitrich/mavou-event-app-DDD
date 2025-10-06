<!-- Begin Footer
================================================== -->
<div class="footer">
    <div class="container">
        <div class="footer-content">
            <p class="footer-text">
                Internship project By Nkwesi Ditrich
            </p>
            <p class="footer-copyright">
                © {{ date('Y') }} Mavou Consulting. All rights reserved.
            </p>
        </div>
    </div>
</div>
<!-- End Footer
================================================== -->

<style>
.footer {
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    color: white;
    padding: 30px 0;
    margin-top: 60px;
    border-top: none;
}

.footer-content {
    text-align: center;
}

.footer-text {
    color: rgba(255, 255, 255, 0.95);
    margin: 0 0 10px 0;
    font-size: 1rem;
    font-weight: 500;
}

.footer-copyright {
    color: rgba(255, 255, 255, 0.8);
    margin: 0;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .footer {
        padding: 25px 0;
        margin-top: 40px;
    }
    
    .footer-text,
    .footer-copyright {
        font-size: 0.85rem;
    }
}
</style>
