import { Component, OnInit, ResolvedReflectiveFactory } from '@angular/core';
import { User } from '../../models/user';
import { UserService } from '../../services/user.service';
import { Router, ActivatedRoute, Params } from '@angular/router';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {
  public title: string;
  public user: User;
  public status: string;
  public token;
  public identity;
  public isAuthenticated: boolean;

  constructor(
      private _userService: UserService,
      private _router: Router,
      private _route: ActivatedRoute,
    ) { 
    this.title = "Logueate";
    this.user = new User(0,'','','ROLE_USER','','','','');
    this.isAuthenticated = this._userService.isAuthenticated();
  }

  ngOnInit(): void {
    if(this.isAuthenticated){
      this._router.navigate(['inicio']);
    }
    
    this.logout();
  }

  onSubmit(form){
    this._userService.login(this.user).subscribe(
      result => {

          this.token = result;

          this._userService.login(this.user, true).subscribe(

            result => {
               this.identity = result;
               this.status = 'success';

               localStorage.setItem('token', this.token);
               localStorage.setItem('identity', JSON.stringify(this.identity));

               this._router.navigate(['/inicio']);
               
            },

            error=>{
              this.status = 'error';
              console.log(<any>error);
            }
          );

        },
        

      error=>{
        this.status= 'error';
        console.log(<any>error);
      }
    );
  }

  logout(){
    this._route.params.subscribe(params =>{
      let logoutSure = +params['sure'];
      
      if(logoutSure === 1){
        localStorage.removeItem('identity');
        localStorage.removeItem('token');
        this._router.navigate(['login']);
      }

    }
      
    );

  }

}
