@extends('front.automation.master')
@section('template_title','automation')
@push('css')
<style>
.select2-container
{
  width: 270px !important;
}
</style>
@endpush
@section('content')
<section class="register my-5">
    <div class="container">
      <h2 class="register-heading" id="dtime"></h2>
      <div class="row">
        <div class="col-md-7 mx-auto">
        <form>
          <div class="row g-2 align-items-center rowst">
            <div class="col-lg-auto  label-right">
              <label  class="col-form-label labelr">Full Name*</label>
            </div>
            <div class="col-lg-auto">
              <input type="text" id="first_name" class="form-control regis topinput" placeholder="First Name" required>
            </div>
            <div class="col-lg-auto">
              <input type="text" id="last_name" class="form-control regis topinput" aria-describedby="lastName2HelpInline" placeholder="Last Name" required>
            </div>
          </div>
          <div class="row g-2 align-items-center rowst">
            <div class="col-xl-auto label-right">
              <label  class="col-form-label labelr">Address*</label>
            </div>
            <div class="col-xl-auto">
              <div class="form-floating">
              <textarea class="form-control address" cols="50" rows="3" required></textarea>
            </div>
            </div>
          </div>
          <div class="row g-2 align-items-center rowst">
            <div class="col-xl-auto label-right">
              <label  class="col-form-label labelr">Email Address*</label>
            </div>
            <div class="col-xl-auto">
              <input type="email" id="email" class="form-control regis" name="auto_email" required>
            </div>
          </div>
          <div class="row g-2 align-items-center rowst">
            <div class="col-xl-auto label-right">
              <label  class="col-form-label labelr">Phone*</label>
            </div>
            <div class="col-xl-auto">
              <input type="phone" id="phone" class="form-control regis" name="auto_phone" required>
            </div>
          </div>
          <div class="row g-2 align-items-center rowst">
            <div class="col-xl-auto label-right">
              <label  class="col-form-label labelr">Gender*</label>
            </div>
            <div class="col-xl-auto">
              <div class="form-check form-check-inline check-radio pl-0" id="gender">
                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="male" value="option1">
                <label class="form-check-label" for="male">Male</label>
              </div>
              <div class="form-check form-check-inline check-radio pl-0">
                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="female" value="option2" required="required">
                <label class="form-check-label" for="female">Female</label>
              </div>
            </div>
          </div>
          <div class="row g-2 align-items-center rowst">
            <div class="col-xl-auto label-right">
              <label class="col-form-label labelr">Hobbies</label>
            </div>
            <div class="col-xl-auto">
              <div class="form-check check-radio">
                <input class="form-check-input" type="checkbox" value="" id="checkbox-cricket">
                <label class="form-check-label" for="checkbox-cricket">
                  Cricket
                </label>
              </div>
              <div class="form-check check-radio">
                <input class="form-check-input" type="checkbox" value="" id="checkbox-movie">
                <label class="form-check-label" for="checkbox-movie">
                Movies
                </label>
              </div>
              <div class="form-check check-radio">
                <input class="form-check-input" type="checkbox" value="" id="checkbox-hockey">
                <label class="form-check-label" for="checkbox-hockey">
                  Hockey
                </label>
              </div>
            </div>
          </div>
          <div class="row g-2 align-items-center rowst">
            <div class="col-xl-auto label-right">
              <label class="col-form-label labelr">Languages</label>
            </div>
            <div class="col-xl-auto">
              <select class="form-select select_2" id="languages" multiple required>
                <option value="">Select Language</option>
                <option value="english">Arabic</option>
                <option value="hindi">Bulgarian</option>
                <option value="hindi">Catalan</option>
                <option value="hindi">Croatian</option>
                <option value="hindi">Italian</option>
                <option value="hindi">English</option>
                <option value="hindi">Hindi</option>
                <option value="hindi">French</option>
                <option value="hindi">German</option>
            </select>
            </div>
          </div>
          <div class="row g-2 align-items-center rowst">
            <div class="col-xl-auto label-right">
              <label  class="col-form-label labelr">Skills</label>
            </div>
            <div class="col-xl-auto">
              <select class="form-select s-height" id="skills" data-testid="skill" multiple required>
                <option value="">Select Skills</option>
            <option value="Adobe InDesign">Adobe InDesign</option>
            <option value="Adobe Photoshop">Adobe Photoshop</option>
            <option value="Analytics">Analytics</option>
            <option value="Android">Android</option>
            <option value="APIs">APIs</option>
            <option value="Art Design">Art Design</option>
            <option value="AutoCAD">AutoCAD</option>
            <option value="Backup Management">Backup Management</option>
            <option value="C">C</option>
            <option value="C++">C++</option>
            <option value="Certifications">Certifications</option>
            <option value="Client Server">Client Server</option>
            <option value="Client Support">Client Support</option>
            <option value="Configuration">Configuration</option>
            <option value="Content Managment">Content Managment</option>
            <option value="Content Management Systems (CMS)">Content Management Systems (CMS)</option>
            <option value="Corel Draw">Corel Draw</option>
            <option value="Corel Word Perfect">Corel Word Perfect</option>
            <option value="CSS">CSS</option>
            <option value="Data Analytics">Data Analytics</option>
            <option value="Desktop Publishing">Desktop Publishing</option>
            <option value="Design">Design</option>
            <option value="Diagnostics">Diagnostics</option>
            <option value="Documentation">Documentation</option>
            <option value="End User Support">End User Support</option>
            <option value="Email">Email</option>
            <option value="Engineering">Engineering</option>
            <option value="Excel">Excel</option>
            <option value="FileMaker Pro">FileMaker Pro</option>
            <option value="Fortran">Fortran</option>
            <option value="HTML">HTML</option>
            <option value="Implementation">Implementation</option>
            <option value="Installation">Installation</option>
            <option value="Internet">Internet</option>
            <option value="iOS">iOS</option>
            <option value="iPhone">iPhone</option>
            <option value="Linux">Linux</option>
            <option value="Java">Java</option>
            <option value="Javascript">Javascript</option>
            <option value="Mac">Mac</option>
            <option value="Matlab">Matlab</option>
            <option value="Maya">Maya</option>
            <option value="Microsoft Excel">Microsoft Excel</option>
            <option value="Microsoft Office">Microsoft Office</option>
            <option value="Microsoft Outlook">Microsoft Outlook</option>
            <option value="Microsoft Publisher">Microsoft Publisher</option>
            <option value="Microsoft Word">Microsoft Word</option>
            <option value="Microsoft Visual">Microsoft Visual</option>
            <option value="Mobile">Mobile</option><option value="MySQL">MySQL</option>
            <option value="Networks">Networks</option>
            <option value="Open Source Software">Open Source Software</option>
            <option value="Oracle">Oracle</option>
            <option value="Perl">Perl</option>
            <option value="PHP">PHP</option>
            <option value="Presentations">Presentations</option>
            <option value="Processing">Processing</option>
            <option value="Programming">Programming</option>
            <option value="PT Modeler">PT Modeler</option>
            <option value="Python">Python</option>
            <option value="QuickBooks">QuickBooks</option>
            <option value="Ruby">Ruby</option>
            <option value="Shade">Shade</option>
            <option value="Software">Software</option>
            <option value="Spreadsheet">Spreadsheet</option>
            <option value="SQL">SQL</option>
            <option value="Support">Support</option>
            <option value="Systems Administration">Systems Administration</option>
            <option value="Tech Support">Tech Support</option>
            <option value="Troubleshooting">Troubleshooting</option>
            <option value="Unix">Unix</option>
            <option value="UI / UX">UI / UX</option>
            <option value="Web Page Design">Web Page Design</option>
            <option value="Windows">Windows</option>
            <option value="Word Processing">Word Processing</option>
            <option value="XML">XML</option>
            <option value="XHTML">XHTML</option>
            </select>
            </div>
          </div>
          <div class="row g-2 align-items-center rowst">
            <div class="col-xl-auto label-right">
              <label  class="col-form-label labelr">Select Country:</label>
            </div>
            <div class="col-xl-auto">
              <select class="form-select" id="country-list" aria-label="Default select example" required>
                <option></option>
                @foreach ($countries as $country)
                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="row g-2 align-items-center rowst">
            <div class="col-xl-auto label-right">
              <label  class="col-form-label labelr">Date Of Birth</label>
            </div>
            <div class="col-xl-auto">
            <select class="form-select" id="dob" aria-label="Default select example" required>
              <option value="">Year </option>
              <option value="1916">1916</option><option value="1917">1917</option><option value="1918">1918</option><option value="1919">1919</option><option value="1920">1920</option><option value="1921">1921</option><option value="1922">1922</option><option value="1923">1923</option><option value="1924">1924</option><option value="1925">1925</option><option value="1926">1926</option><option value="1927">1927</option><option value="1928">1928</option><option value="1929">1929</option><option value="1930">1930</option><option value="1931">1931</option><option value="1932">1932</option><option value="1933">1933</option><option value="1934">1934</option><option value="1935">1935</option><option value="1936">1936</option><option value="1937">1937</option><option value="1938">1938</option><option value="1939">1939</option><option value="1940">1940</option><option value="1941">1941</option><option value="1942">1942</option><option value="1943">1943</option><option value="1944">1944</option><option value="1945">1945</option><option value="1946">1946</option><option value="1947">1947</option><option value="1948">1948</option><option value="1949">1949</option><option value="1950">1950</option><option value="1951">1951</option><option value="1952">1952</option><option value="1953">1953</option><option value="1954">1954</option><option value="1955">1955</option><option value="1956">1956</option><option value="1957">1957</option><option value="1958">1958</option><option value="1959">1959</option><option value="1960">1960</option><option value="1961">1961</option><option value="1962">1962</option><option value="1963">1963</option><option value="1964">1964</option><option value="1965">1965</option><option value="1966">1966</option><option value="1967">1967</option><option value="1968">1968</option><option value="1969">1969</option><option value="1970">1970</option><option value="1971">1971</option><option value="1972">1972</option><option value="1973">1973</option><option value="1974">1974</option><option value="1975">1975</option><option value="1976">1976</option><option value="1977">1977</option><option value="1978">1978</option><option value="1979">1979</option><option value="1980">1980</option><option value="1981">1981</option><option value="1982">1982</option><option value="1983">1983</option><option value="1984">1984</option><option value="1985">1985</option><option value="1986">1986</option><option value="1987">1987</option><option value="1988">1988</option><option value="1989">1989</option><option value="1990">1990</option><option value="1991">1991</option><option value="1992">1992</option><option value="1993">1993</option><option value="1994">1994</option><option value="1995">1995</option><option value="1996">1996</option><option value="1997">1997</option><option value="1998">1998</option><option value="1999">1999</option><option value="2000">2000</option><option value="2001">2001</option><option value="2002">2002</option><option value="2003">2003</option><option value="2004">2004</option><option value="2005">2005</option><option value="2006">2006</option><option value="2007">2007</option><option value="2008">2008</option><option value="2009">2009</option><option value="2010">2010</option><option value="2011">2011</option><option value="2012">2012</option><option value="2013">2013</option><option value="2014">2014</option><option value="2015">2015</option>
            </select>
            </div>
            <div class="col-xl-auto">
            <select class="form-select" id="months" aria-label="Default select example" required> 
                <option value="">Month</option>
               <option>January</option>
               <option>February</option>
               <option>March</option>
               <option>April</option>
               <option>May</option>
               <option>June</option>
               <option>July</option>
               <option>August</option>
               <option>September</option>
               <option>October</option>
               <option>November</option>
               <option>December</option>
            </select>
            </div>
            <div class="col-xl-auto">
            <select class="form-select" id="days" aria-label="Default select example" required>
              <option value=""> Day </option>
               <option value="1">1 </option>
               <option value="2">2</option>
               <option value="3">3</option>
               <option value="4">4</option>
               <option value="5">5</option>
               <option value="6">6</option>
               <option value="7">7</option>
               <option value="8">8</option>
               <option value="9">9</option>
               <option value="10">10</option>
               <option value="11">11</option>
               <option value="12">12</option>
               <option value="13">13</option>
               <option value="14">14</option>
               <option value="15">15</option>
               <option value="16">16</option>
               <option value="17">17</option>
               <option value="18">18</option>
               <option value="19">19</option>
               <option value="20">20</option>
               <option value="21">21</option>
               <option value="22">22</option>
               <option value="23">23</option>
               <option value="24">24</option>
               <option value="25">25</option>
               <option value="26">26</option>
               <option value="27">27</option>
               <option value="28">28</option>
               <option value="29">29</option>
               <option value="30">30</option>
               <option value="31">31</option>
            </select>
            </div>
          </div>
          <div class="row g-2 align-items-center rowst">
            <div class="col-xl-auto label-right">
              <label  class="col-form-label labelr">Password</label>
            </div>
            <div class="col-xl-auto">
                <input type="password" id="password" class="form-control regis" aria-describedby="firstName1HelpInline" required>
            </div>
          </div>
          <div class="row g-2 align-items-center rowst">
            <div class="col-xl-auto label-right">
              <label  class="col-form-label labelr">Confirm Password</label>
            </div>
            <div class="col-xl-auto">
                <input type="Confirm-password" id="confirm-password" class="form-control regis" required>
            </div>
          </div>
            <div class="row g-2 button-rowst">
            <div class="form group buttons-double">
                <button id="submitbtn" type="submit" class="btn btn-primary subref" name="signup"> Submit </button>
                <button id="Button1" type="reset" class="btn btn-primary subref" value="Refresh" >Refresh</button>
              </div>
            </div>
            <div class="row g-2 align-items-center rowst" bis_skin_checked="1"><h6 class="form-success text-success d-none">Form submitted succesfully !!</h6></div>
          </form>
        </div>
        <div class="col-md-3">
          <form action="" class="rightform"></form>
          <h4 class="labelr photo-heding">Photo</h4>
          <img src="{{ asset('front/images/logo.png') }}" id="profile_picture"  alt="form logo right side" class="formlogo">
          <input type="file" name="file" value="" class="choosefile">
        </div>
      </div>
    </div>
</section>
@endsection
@push('js')
<script>
  var date = new Date();
	var current_date = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+ date.getDate()+" "+date.getHours()+":"+date.getMinutes()+":"+date.getSeconds();
    $('#dtime').html('Register - '+current_date);
    $('.select_2').select2({
        placeholder: "Select language",
        closeOnSelect: false
    });

    let form = document.getElementsByTagName("form")[0];
      form.addEventListener("submit", (e) => {
      e.preventDefault();
      $('.form-success').removeClass('d-none');
    });

</script>
@endpush