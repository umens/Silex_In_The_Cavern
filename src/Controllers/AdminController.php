<?php

namespace Controllers;

use Silex\ControllerProviderInterface;
use Silex\Application;

class AdminController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        $controllers->get('/', function (Application $app) {

            return "Brought to you by admin !";
            		
			return $app['twig']->render('dashboard.html.twig', array('title' => 'Administration', 'name' => $userInfo['name']));
        
        })->bind('dashboard');

        $controllers->get('/manage_images', function (Application $app) {

            $images = $app['db']->fetchAll('SELECT p.id, description, url, url_min, date, online, note, u.username FROM picture p left join user u on posted_by = u.id ORDER BY id DESC');
      
            return $app['twig']->render('manage_images.html.twig', array('title' => 'Gestion des images', 'images' => $images));
        
        })->bind('manage_images');

        $controllers->match('/add_image', function (\Symfony\Component\HttpFoundation\Request $request) use ($app) {
            // some default data for when the form is displayed the first time
            $data = array(
                //'description' => 'Petite description ou citation',
            );

            $form = $app['form.factory']->createBuilder('form', $data)
                ->add('description', 'textarea', array(
                        'label' => 'Petite Description/Citation : ',
                        'required'  => true,
                    )
                )
                ->add('image', 'file', array(
                        'label' => 'Les plus belles fesses du monde',
                        'required'  => true,
                    )
                )
                ->add('online', 'checkbox', array(
                        'label' => 'Mettre l\'image en ligne : ', 
                    )

                )
                ->getForm();

            if ('POST' == $request->getMethod()) {
                $form->bind($request);

                if ($form->isValid()) {
                    $data = $form->getData();

                    $path = __DIR__.'/../../web/upload/'.date("Y/m");

                    if(!file_exists($path)){

                        die("non : ".$path);
                        
                        mkdir($path, 774, true);

                    }

                    $filename = "big_".date("d").".".$data['image']->guessExtension();

                    if(file_exists($path."/".$filename)){

                        $exist = false;
                        $i = 1;

                        do{

                            $filename = "big_".date("d")."-".$i.".".$data['image']->guessExtension();
                            if(!file_exists($path."/".$filename)){
                                $exist = true;
                            }else{
                                $i++;
                            }

                        }while(!$exist);

                    }

                    $data['image']->move($path,$filename);

                    $app["upload"]->upload($path."/".$filename, "fr_FR");

                    if($app["upload"]->uploaded) {
                            // save thumnail uploaded image with a new name,
                            // resized to 500px wide
                            if(isset($i) AND $i > 0){

                                $app["upload"]->file_new_name_body = "min_".date("d")."-".$i;

                            }else{
                                
                                $app["upload"]->file_new_name_body = "min_".date("d");

                            }
                            $app["upload"]->image_resize = true;
                            $app["upload"]->image_x = 500;
                            $app["upload"]->image_ratio_y = true;
                            $app["upload"]->Process($path);
                            if ($app["upload"]->processed) {

                                $token = $app['security']->getToken();
                                if (null !== $token) {
                                    $user = $token->getUser()->getUsername();
                                    $user = $app['db']->fetchColumn("SELECT id FROM user WHERE username = '$user' AND roles = 'ROLE_ADMIN'");
                                }

                                $app['db']->insert('picture', array(
                                    'description' => $data["description"],
                                    'url'         => 'upload/'.date("Y/m").'/'.$filename,
                                    'url_min'     => 'upload/'.date("Y/m").'/'.$app["upload"]->file_dst_name,
                                    'date'        => date("Y-m-d"),
                                    'online'      => 1,
                                    'note'        => 0,
                                    'posted_by'   => $user
                                ));

                                $app['session']->getFlashBag()->add(
                                    'success',
                                    array(
                                        'title'   => 'Bravo !',
                                        'message' => 'L\'image a bien été uploadé et ajouté sur le site.',
                                    )
                                );
                            
                            } else {

                                $app['session']->getFlashBag()->add(
                                    'error',
                                    array(
                                        'title'   => 'Erreur !',
                                        'message' => $app["upload"]->error,
                                    )
                                );

                            }

                    }

                    return $app->redirect('/admin/manage_images');
                }
            }

            // display the form
            return $app['twig']->render('add_image.html.twig', array('title' => 'Ajouter une image', 'form' => $form->createView()));

        })->bind('add_image');

        $controllers->match('/edit_image/{id}', function (\Symfony\Component\HttpFoundation\Request $request, $id) use ($app) {

            // some default data for when the form is displayed the first time
            $data = $app["db"]->fetchAssoc("SELECT * FROM picture WHERE id= $id");
            if($data["online"] == 1){
                $data["online"] = true;
            }
            else{
                $data["online"] = false;
            }

            $form = $app['form.factory']->createBuilder('form', $data)
                ->add('description', 'textarea', array(
                        'label' => 'Petite Description/Citation : ',
                        'required'  => true,
                    )
                )
                ->add('image', 'file', array(
                        'label' => 'Les plus belles fesses du monde',
                    )
                )
                ->add('online', 'checkbox', array(
                        'label' => 'Mettre l\'image en ligne : ', 
                    )
                )
                ->add('date', 'date', array(
                        'label' => 'Date de mise en ligne : ', 
                        'input' => 'string',
                        'format' => 'dd-MM-yyyy',
                    )

                )
                ->getForm();

            $date = explode("-", $data["date"]);

            if ('POST' == $request->getMethod()) {
                $form->bind($request);

                if ($form->isValid()) {
                    $data = $form->getData();

                    $path = __DIR__."/../../web/upload/".$date['0']."/".$date['1'];

                    if(!file_exists($path)){
                        
                        mkdir($path, 774, true);

                    }

                    $filename = "big_".$date['2'].".".$data['image']->guessExtension();

                    if(file_exists($path."/".$filename)){

                        $exist = false;
                        $i = 1;

                        do{

                            $filename = "big_".$date['2']."-".$i.".".$data['image']->guessExtension();
                            if(!file_exists($path."/".$filename)){
                                $exist = true;
                            }else{
                                $i++;
                            }

                        }while(!$exist);

                    }

                    $data['image']->move($path,$filename);

                    $app["upload"]->upload($path."/".$filename, "fr_FR");

                    if($app["upload"]->uploaded) {
                            // save thumnail uploaded image with a new name,
                            // resized to 500px wide
                            if(isset($i) AND $i > 0){

                                $app["upload"]->file_new_name_body = "min_".$date['2']."-".$i;

                            }else{
                                
                                $app["upload"]->file_new_name_body = "min_".$date['2'];

                            }
                            $app["upload"]->image_resize = true;
                            $app["upload"]->image_x = 500;
                            $app["upload"]->image_ratio_y = true;
                            $app["upload"]->Process($path);
                            if ($app["upload"]->processed) {

                                $app['db']->update('picture', array(
                                    'description' => $data["description"],
                                    'url'         => 'upload/'.$date["0"].'/'.$date["1"].'/'.$filename,
                                    'url_min'     => 'upload/'.$date["0"].'/'.$date["1"].'/'.$app["upload"]->file_dst_name,
                                ), array( 'id' => $data['id']));

                                $app['session']->getFlashBag()->add(
                                    'success',
                                    array(
                                        'title'   => 'Bravo !',
                                        'message' => 'L\'image a bien été modifié et mise à jour sur le site.',
                                    )
                                );
                            
                            } else {

                                $app['session']->getFlashBag()->add(
                                    'error',
                                    array(
                                        'title'   => 'Erreur !',
                                        'message' => $app["upload"]->error,
                                    )
                                );

                            }

                    }

                    return $app->redirect('/admin/manage_images');
                }
            }

            // display the form
            return $app['twig']->render('edit_image.html.twig', array('title' => 'Editer une image', 'id' => $id, 'form' => $form->createView()));

        })->bind('edit_image');

        $controllers->get('/delete_image/{id}', function ($id) use ($app) {

            $urls = $app['db']->fetchAssoc("SELECT url, url_min FROM picture WHERE id = $id");

            unlink(__DIR__."/../../web/".$urls["url"]);
            unlink(__DIR__."/../../web/".$urls["url_min"]);

            if($app['db']->delete('picture', array('id' => $id))){
      
                $app['session']->getFlashBag()->add(
                    'success',
                    array(
                        'title'   => 'Bravo !',
                        'message' => 'L\'image a bien été supprimée.',
                    )
                );

            }
            else{

                $app['session']->getFlashBag()->add(
                    'error',
                    array(
                        'title'   => 'Erreur !',
                        'message' => 'Il y a eu un probleme, recommencez l\'opération.',
                    )
                );

            }

            return $app->redirect('/admin/manage_images');
        
        })->bind('delete_image');

        $controllers->get('/put_online/{id}', function ($id) use ($app) {

            if($app['db']->update('picture', array('online' => '1'), array('id' => $id))){

                $app['session']->getFlashBag()->add(
                    'success',
                    array(
                        'title'   => 'Bravo !',
                        'message' => 'L\'image a bien été mise en ligne sur le site.',
                    )
                );

            }
            else{

                $app['session']->getFlashBag()->add(
                    'error',
                    array(
                        'title'   => 'Erreur !',
                        'message' => 'Il y a eu un probleme, recommencez l\'opération.',
                    )
                );

            }
      
            return $app->redirect('/admin/manage_images');
        
        })->bind('put_online');

        $controllers->get('/put_offline/{id}', function ($id) use ($app) {

            if($app['db']->update('picture', array('online' => '0'), array('id' => $id))){

                $app['session']->getFlashBag()->add(
                    'success',
                    array(
                        'title'   => 'Bravo !',
                        'message' => 'L\'image a bien été mise hors-ligne et retiré du site.',
                    )
                );

            }
            else{

                $app['session']->getFlashBag()->add(
                    'error',
                    array(
                        'title'   => 'Erreur !',
                        'message' => 'Il y a eu un probleme, recommencez l\'opération.',
                    )
                );

            }
      
            return $app->redirect('/admin/manage_images');
        
        })->bind('put_offline');

        return $controllers;
    }
}
