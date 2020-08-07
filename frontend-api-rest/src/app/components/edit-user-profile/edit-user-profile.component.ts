import { Component, OnInit } from '@angular/core';
import { User } from '../../models/user';
import { UserService } from '../../services/user.service';

@Component({
  selector: 'app-edit-user-profile',
  templateUrl: './edit-user-profile.component.html',
  styleUrls: ['./edit-user-profile.component.css']
})
export class EditUserProfileComponent implements OnInit {
  public title: string;
  public user: User;
  public isAuthenticated: boolean;
  public identity;
  public token;
  public status: string;

  constructor(private _userService: UserService) {
    this.title = "Ajustes de usuario";
    this.identity = this._userService.getIdentity();
    this.user = new User(this.identity.sub, this.identity.name, 
                        this.identity.surname, 'ROLE_USER',this.identity.email,
                        '',this.identity.description,this.identity.image
                        );

    this.isAuthenticated = this._userService.isAuthenticated();
    this.token = this._userService.getToken();
    //Obtiene la identidad del usuario logueado
    
   }

  ngOnInit(): void {
  }

  onSubmit(form){
    this._userService.update(this.user, this.token).subscribe( response => {
    
      if(response && response.status == 'success'){
        this.identity = response.user_update[0];
        localStorage.setItem('identity', JSON.stringify(this.identity));
        console.log(response.user_update[0]);
       // ------------------------------TERMINAR ESTE METODO------------------------------------------------------------
      }
      
    }, error=>{
      console.log(<any>error);
    }

    );
  }

}
