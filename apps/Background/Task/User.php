<?php
namespace Background\Task;

use SeuDo\Main;
use SeuDo\Queue;

class User extends BackgroundBase {
    public function executeSendEmailVerify() {
        try{
            print "START\n";

            //fix data send email
            /*
            $user_id = 40;//luutronghieu
            $user = \Users::retrieveById($user_id);
            if($user instanceof \Users){
                $email = $user->getEmail();
                $emailActivity = new \EmailActivities();

                $activity = $emailActivity->setNewActivitis($email, $user->getId());

                if($activity){
                    $t = base64_encode($user->getId().'-'.($activity->getCode()));
                    $link = Main::getHomeUrl().'register/success_verify?t='.$t;
                    \UserMailUtil::pushVerifyEmail($user->getUsername(), $email, $link, $user->getFullName());
                    print "Send mail to " . $email . "\n";
                }
            }
            */

            $query = \Users::select();
            $query->andWhere("verify_email = 0");
            $users = $query->execute();

            $success = $error = 0;

            if(sizeof($users) > 0){
                foreach($users as $user){
                    if($user instanceof \Users){

                        print "ITEM EMAIL ACTIVITY - " . $user->getId() . "\n";

                        $email = $user->getEmail();
                        $emailActivity = new \EmailActivities();

                        $activity = $emailActivity->setNewActivitis($email, $user->getId());

                        if($activity){
                            $t = base64_encode($user->getId().'-'.($activity->getCode()));
                            $link = Main::getHomeUrl().'register/success_verify?t='.$t;
                            $check = \UserMailUtil::pushVerifyEmail($user->getUsername(), $email, $link, $user->getFullName());
                            if($check){
                                print "Send mail to " . $email . " - SUCCESS\n";
                                $success++;
                            }else{
                                print "Send mail to " . $email . " - ERROR\n";
                                $error++;
                            }
                        }

                    }
                }

                print "Total send success: " . $success . "\n";
                print "Total send error: " . $error . "\n";
            }

            print "END\n";
        }catch (\Exception $e){
            print "ERROR\n" . $e->getMessage();
        }

    }

}
