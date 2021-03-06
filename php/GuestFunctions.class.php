<?php
   class GuestActions{
      private $lists;

      public function __construct($lists) {
         $this->lists = $lists;
      }

      public function Login($postObject){
         $credentials = [
            'username' => $postObject['username'],
            'password' => $postObject['password']
         ];

         $joomlaApp = JFactory::getApplication('site');
         
         $db = JFactory::getDbo();
         $query = $db->getQuery(true)
            ->select('id, password')
            ->from('#__users')
            ->where('username=' . $db->quote($credentials['username']));
      
         $db->setQuery($query);
         $result = $db->loadObject();
         if ($result) {
            $match = JUserHelper::verifyPassword($credentials['password'], $result->password, $result->id);
            if ($match === true) {
               $joomlaApp->login($credentials);
               $dataLists = $this->lists->GetLists();
               return ['message' => "Opnieuw ingelogd", 'dataLists' => $dataLists];
            }
            else {      
               throw new Exception('Fout wachtwoord, probeer het nog eens');
            }
         } else {
            throw new Exception("Gebruiker '" . $credentials['username'] . "' bestaat niet");
         }
      }

      public function CheckIfuserIsLoggedIn(){
         $user = JFactory::getUser();
         $dataLists = $this->lists->GetLists();
         return ['userIsloggedIn' => $user->id != 0, 'dataLists' => $dataLists];
      }
   }