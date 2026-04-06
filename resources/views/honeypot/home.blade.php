<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ config('honeypot.company.name') }} — {{ config('honeypot.company.tagline') }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
  .hero{background:linear-gradient(135deg,#0d6efd 0%,#0a3d91 100%);padding:100px 0;}
  .service-card{border:none;border-radius:12px;transition:transform .2s,box-shadow .2s;}
  .service-card:hover{transform:translateY(-4px);box-shadow:0 8px 24px rgba(0,0,0,.12);}
  .team-img{width:100px;height:100px;object-fit:cover;border-radius:50%;}
  footer{background:#1a1a2e;}
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold fs-4" href="/"><i class="bi bi-cpu me-2"></i>{{ config('honeypot.company.name') }}</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link active" href="/">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="/about">About</a></li>
        <li class="nav-item"><a class="nav-link" href="/products">Products</a></li>
        <li class="nav-item"><a class="nav-link" href="/services">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="/contact">Contact</a></li>
      </ul>
      <a class="btn btn-outline-light btn-sm" href="/login">Staff Login</a>
    </div>
  </div>
</nav>

<!-- Hero -->
<section class="hero text-white text-center">
  <div class="container">
    <h1 class="display-4 fw-bold mb-3">{{ config('honeypot.company.tagline') }}</h1>
    <p class="lead mb-4">Enterprise cloud, security, and custom software solutions trusted by 500+ companies worldwide since {{ config('honeypot.company.founded') }}.</p>
    <a href="/contact" class="btn btn-light btn-lg me-2">Get a Free Consultation</a>
    <a href="/products" class="btn btn-outline-light btn-lg">View Products</a>
  </div>
</section>

<!-- Services -->
<section class="py-5">
  <div class="container">
    <h2 class="text-center fw-bold mb-5">Our Services</h2>
    <div class="row g-4">
      @foreach([
        ['bi-cloud-upload','Cloud Migration','Move your infrastructure to AWS, Azure, or GCP with zero downtime.'],
        ['bi-shield-lock','Cybersecurity','Penetration testing, SIEM, and 24/7 SOC-as-a-Service.'],
        ['bi-code-slash','Custom Development','Full-stack web & mobile apps tailored to your workflows.'],
        ['bi-graph-up','Data Analytics','Business intelligence dashboards and ML pipelines.'],
        ['bi-people','IT Consulting','CTO-as-a-Service and digital transformation roadmaps.'],
        ['bi-headset','Managed Support','Round-the-clock infrastructure monitoring and SLA-backed support.'],
      ] as [$icon,$title,$desc])
      <div class="col-md-4">
        <div class="card service-card p-4 h-100">
          <i class="bi {{ $icon }} fs-1 text-primary mb-3"></i>
          <h5 class="fw-bold">{{ $title }}</h5>
          <p class="text-muted mb-0">{{ $desc }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- Stats -->
<section class="bg-primary text-white py-5">
  <div class="container">
    <div class="row text-center g-4">
      <div class="col-md-3"><h2 class="display-5 fw-bold">500+</h2><p>Enterprise Clients</p></div>
      <div class="col-md-3"><h2 class="display-5 fw-bold">12+</h2><p>Years of Experience</p></div>
      <div class="col-md-3"><h2 class="display-5 fw-bold">99.9%</h2><p>Uptime SLA</p></div>
      <div class="col-md-3"><h2 class="display-5 fw-bold">150+</h2><p>Team Members</p></div>
    </div>
  </div>
</section>

<!-- Team -->
<section class="py-5 bg-light">
  <div class="container">
    <h2 class="text-center fw-bold mb-5">Leadership Team</h2>
    <div class="row g-4 justify-content-center">
      @foreach([
        ['Robert Anderson','Chief Executive Officer','robert.anderson'],
        ['Sarah Chen','Chief Technology Officer','sarah.chen'],
        ['Michael Torres','Chief Information Security Officer','michael.torres'],
        ['Emily Walsh','VP of Engineering','emily.walsh'],
      ] as [$name,$role,$user])
      <div class="col-md-3 text-center">
        <img src="https://ui-avatars.com/api/?name={{ urlencode($name) }}&size=100&background=0d6efd&color=fff&rounded=true" class="team-img mb-3" alt="{{ $name }}">
        <h6 class="fw-bold mb-0">{{ $name }}</h6>
        <small class="text-muted">{{ $role }}</small><br>
        <small class="text-primary">{{ $user }}@{{ config('honeypot.company.domain') }}</small>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- Contact CTA -->
<section class="py-5">
  <div class="container text-center">
    <h2 class="fw-bold mb-3">Ready to transform your business?</h2>
    <p class="text-muted mb-4">Contact us at <a href="mailto:{{ config('honeypot.company.email') }}">{{ config('honeypot.company.email') }}</a> or call {{ config('honeypot.company.phone') }}</p>
    <a href="/contact" class="btn btn-primary btn-lg">Contact Us Today</a>
  </div>
</section>

<!-- Footer -->
<footer class="text-white py-4">
  <div class="container">
    <div class="row">
      <div class="col-md-4">
        <strong>{{ config('honeypot.company.name') }}</strong>
        <p class="text-muted small mt-2">{{ config('honeypot.company.location') }}<br>{{ config('honeypot.company.phone') }}</p>
      </div>
      <div class="col-md-4">
        <strong>Quick Links</strong>
        <ul class="list-unstyled mt-2 small">
          <li><a href="/" class="text-muted text-decoration-none">Home</a></li>
          <li><a href="/about" class="text-muted text-decoration-none">About</a></li>
          <li><a href="/contact" class="text-muted text-decoration-none">Contact</a></li>
        </ul>
      </div>
      <div class="col-md-4">
        <strong>Staff Access</strong>
        <ul class="list-unstyled mt-2 small">
          <li><a href="/login" class="text-muted text-decoration-none">Staff Portal</a></li>
          <li><a href="/wp-admin" class="text-muted text-decoration-none">CMS Admin</a></li>
          <li><a href="/admin" class="text-muted text-decoration-none">Admin Panel</a></li>
        </ul>
      </div>
    </div>
    <hr class="border-secondary">
    <p class="text-muted text-center small mb-0">&copy; {{ date('Y') }} {{ config('honeypot.company.name') }}. Powered by WordPress {{ config('honeypot.company.wp_version') }}.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
