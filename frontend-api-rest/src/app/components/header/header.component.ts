import { Component, OnInit, DoCheck } from '@angular/core';
import { UserService } from '../../services/user.service';
import { User } from '../../models/user';

@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.css'],
  providers: [UserService]
})
export class HeaderComponent implements OnInit, DoCheck {
  public identity;
  public token;

  constructor(private _userService: UserService){
    this.setIdentities();
  }

  ngOnInit(): void {
    
  }

  ngDoCheck(){
    this.setIdentities();
  }

  setIdentities(){
    this.identity = this._userService.getIdentity();
    this.token = this._userService.getToken();
  }
}
