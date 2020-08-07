import { Component, OnInit } from '@angular/core';
import { User } from '../../models/user';
import { UserService } from '../../services/user.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css'],
  providers: [UserService]
})

export class RegisterComponent implements OnInit {

  public title: string;
  public user: User;
  public status: string;
  public isAuthenticated: boolean;

  constructor(private _userService: UserService, private _router: Router) { 
    this.title = "Registro";
    this.user = new User(1,'','','ROLE_USER','','','','');
    this.isAuthenticated = this._userService.isAuthenticated();
  }

  ngOnInit(): void {
    if(this.isAuthenticated){
      this._router.navigate(['/inicio']);
    }
  }

  onSubmit(form){
    this._userService.register(this.user).subscribe( result =>
       {
          this.status = result.status === 'success' ? result.status : 'error';   
       },
       error => {
         this.status = 'error';
         console.log(error);
       }
    );
    form.reset();
  }
}
