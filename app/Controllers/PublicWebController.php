<?php

require_once __DIR__ . '/../Helpers/View.php';

class PublicWebController {

    public function home(): void {
        View::render('web/home', [
            'title' => 'Gazoma Pay — Global Access to Zero-Friction Digital Payments'
        ], 'layouts/web');
    }

    public function solutions(): void {
        View::render('web/solutions', [
            'title' => 'Solutions — Payment Infrastructure for Modern Businesses'
        ], 'layouts/web');
    }

    public function pricing(): void {
        View::render('web/pricing', [
            'title' => 'Transparent Pricing — Gazoma Pay'
        ], 'layouts/web');
    }

    public function developers(): void {
        View::render('web/developers', [
            'title' => 'Developer Center & API Docs — Gazoma Pay'
        ], 'layouts/web');
    }

    public function about(): void {
        View::render('web/about', [
            'title' => 'About Us — Gazoma Pay'
        ], 'layouts/web');
    }

    public function security(): void {
        View::render('web/security', [
            'title' => 'Security & Financial Integrity — Gazoma Pay'
        ], 'layouts/web');
    }

    public function contact(): void {
        View::render('web/contact', [
            'title' => 'Contact Sales & Support — Gazoma Pay'
        ], 'layouts/web');
    }
}
