<footer id="footer">
      <div class="container">
        <div class="row topfooter">
          <div class="col-md-3"><a href="{{ URL::to('/')}}">
          @if($result['commonContent']['setting'][15]->value)
                            <img src="{{asset('').$result['commonContent']['setting'][15]->value}}" alt="<?=stripslashes($result['commonContent']['setting'][79]->value)?>" class="img-fluid footer-logo">
          @else
          <img src="{{asset('public')}}/images/tp-logo.jpg" class="img-fluid footer-logo">
          @endif
          </a></div>
          <div class="col-md-6 offset-md-3 pdlf50">
            <div class="row">
          <div class="col-md-5 pdtp10"><strong>Phone Support</strong> <br>
          <h3>1300 71 81 91</h3></div>
          <div class="col-md-7 footerabout">
            <p>Monday to Friday   <span class="float-right">8am - 5pm</span><br>
            Saturday        <span class="float-right">8am - 12pm</span><br>
            Sunday          <span class="float-right">Closed</span><br>
            31 Progress Street, Mornington, Vic 3931 Look for the big green hammer!</p>
          </div>
        </div>
        </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="footermenu">
              <p> 
                <a href="index.php">Home</a>
                <a href="#">All Products</a>
                <a href="#">Products by Trade</a>
                <a href="#">Balustrade</a>
                <a href="#">Pool Fencing</a>
                <a href="#">Calculator</a>
                <a href="gallery.php">Gallery</a>
                <a href="favourites.php">Favourites</a>
                <a href="#">Account</a>
                <a href="#">Contact us </a></p>
            </div>
          </div>
        </div>
            <div class="copyright">
              <div class="row">
                <div class="col-md-6">
                  <p> © @lang('website.Copy Rights') <a href="https://www.modernvisual.com.au" target="_blank" class="web"> @lang('website.Modern Visual')</a></p>
                </div>
                <div class="col-md-6 text-right">
                <p> 
                <a href="#">Return Policy</a>
                <a href="#">Shipping & Delivery</a>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms & Conditions</a>
                </p>
              </div>
              </div>
            </div>
      </div>
    </footer>