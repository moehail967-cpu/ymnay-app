@extends('landlord.frontend.frontend-page-master')
@section('title')
    {{__('Login')}}
@endsection
@section('page-title')
    {{__('Login')}}
@endsection
@section('style')
<style>
.social-login-divider{display:flex;align-items:center;margin:18px 0;gap:10px;color:#999;font-size:13px}
.social-login-divider::before,.social-login-divider::after{content:'';flex:1;height:1px;background:#e5e5e5}
.btn-google-login{display:flex;align-items:center;justify-content:center;width:100%;padding:10px 16px;border:1px solid #dadce0;border-radius:6px;background:#fff;color:#3c4043;font-size:14px;font-weight:500;text-decoration:none;transition:background .15s,box-shadow .15s}
.btn-google-login:hover{background:#f8f9fa;box-shadow:0 1px 4px rgba(0,0,0,.1);color:#3c4043;text-decoration:none}
</style>
@endsection
@section('content')
    <section class="login-page-area padding-top-120 padding-bottom-120">
        <div class="container-max">
            <div class="row justify-content-center">
               <div class="col-lg-6">
                   <div class="contact-form-wrapper">
                       <h2 class="title">{{__("Login to your account")}}</h2>

                       <div id="msg-wrapper"></div>
                       <form action="#">
                           <div class="form-group">
                               <label>{{__('Email or Username')}}</label>
                               <input type="text" name="email" placeholder="{{__('type email or username')}}">
                           </div>
                           <div class="form-group with-icon password-group">
                               <label>{{__('Password')}}</label>
                               <input type="password" name="password" placeholder="{{__('type your password')}}">
                               <span class="icon"><i class="las la-eye"></i></span>
                           </div>
                           <div class="info-wrapper">
                               <div class="left-wrap">
                                   <div class="form-group form-check">
                                       <input type="checkbox" name="remember" class="form-check-input" id="remember_me">
                                       <label class="form-check-label" for="remember_me">{{__('Remember me')}}</label>
                                   </div>
                               </div>
                               <div class="right-wrap">
                                   <a href="#">{{__('Forget Password ?')}}</a>
                               </div>
                           </div>
                           <div class="btn-wrapper">
                               <button type="button" id="login_button" class="btn-default">{{__('Login')}}</button>
                           </div>
                           <div class="extra-wrap margin-top-20">
                               <span>{{__('Do not have account?')}} <a href="{{route('landlord.user.register')}}">{{__('Signup now')}}</a></span>
                           </div>
                       </form>

                       @include('socialauth::partials.google-button')
                   </div>
               </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
            var loginFormButton =  document.getElementById('login_button');
            loginFormButton.addEventListener('click',function (event){
            event.preventDefault();

            var msgWrap = document.getElementById('msg-wrapper');
            msgWrap.innerHTML= '';
            loginFormButton.innerText = "{{__('Connecting...')}}"

        axios({
            url : "{{route('login')}}",
            method : 'post',
            responseType: 'json',
            data : {
            email: document.querySelector('input[name="email"]').value,
            password: document.querySelector('input[name="password"]').value,
            remember: document.querySelector('input[name="remember"]').value,
        }
        }).then(function(res){
            loginFormButton.innerText = "{{__('Redirecting..')}}"
            window.location.reload();
        }).catch(function (error){
            loginFormButton.innerText = "{{__('Login')}}"
            var responseData = error.response.data.errors;
            var child = '<ul class="alert alert-danger">'
            Object.entries(responseData).forEach(function (value){
            child += '<li>'+value[1]+'</li>';
        });
            child += '</ul>'
            msgWrap.innerHTML = child;
        });
        })

    </script>
@endsection
