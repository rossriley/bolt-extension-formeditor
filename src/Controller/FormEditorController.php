<?php

namespace Bolt\Extensions\Ross\FormEditor\Controller;

use Bolt\Controller\Zone;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Bolt\Extension\Bolt\BoltForms\BoltFormsExtension;
use Bolt\Extensions\Ross\FormEditor\Form;
use Bolt\Translation\Translator as Trans;

class FormEditorController implements ControllerProviderInterface
{
    public $app;
    public $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * Sets up all the named routes for the extension.
     *
     * @param Silex\Application $app
     *
     * @return Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        $this->app = $app;

        $ctr = $app['controllers_factory'];
        $ctr->value(Zone::KEY, Zone::BACKEND);

        $ctr->get('/', array($this, 'index'))
            ->bind('formeditor.index');

        $ctr->post('/', array($this, 'index'))
            ->bind('formeditor.create');

        $ctr->get('/edit/{formname}', array($this, 'edit'))
            ->bind('formeditor.edit');

        $ctr->post('/edit/{formname}', array($this, 'edit'))
            ->bind('formeditor.save');

        $ctr->post('/delete/{formname}', array($this, 'delete'))
            ->bind('formeditor.delete');

        $ctr->before(array($this, 'before'));

        return $ctr;
    }

    /**
     * Controller before render
     *
     * @param Request            $request
     * @param \Silex\Application $app
     */
    public function before(Request $request, Application $app)
    {

    }

    /**
     * Index page for extension, lists current forms and provides a
     * form to create a new one.
     *
     * @param Application $apd
     * @param Request     $request
     *
     * @return Response
     */
    public function index(Application $app, Request $request)
    {
        $data = $this->read();

        $form = $app['form.factory']
            ->createBuilder('form')
            ->add('name', 'text', ['required' => true])
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $data = $request->request->get($form->getName());
            if ($data) {
                $this->createForm($data);
            } else {
                $this->app['session']->getFlashBag()->set('error', Trans::__('Unable to create form'));
            }
        }

        return $app['render']->render('@formeditor/index.twig', [
            'forms' => $this->getForms(),
            'create' => $form->createView(),
        ]);
    }

    /**
     * Form edit page.
     *
     * @param Application $app
     * @param Request     $request
     * @param string      $formname The name of the form
     *
     * @return Response
     */
    public function edit(Application $app, Request $request, $formname)
    {
        $form = $this->buildForm($formname);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->get($form->getName());
            $this->save($data, $formname);
            $this->app['session']->getFlashBag()->set('success', Trans::__('Form successfully updated'));
            $form = $this->buildForm($formname);
        }

        return $app['render']->render('@formeditor/edit.twig', [
            'form' => $form->createView(),
            'formname' => $formname,
        ]);
    }

    /**
     * POST method that handles the deletion of a form.
     *
     * @param Application $app
     * @param Request     $request
     * @param string      $formname name of the form
     *
     * @return RedirectResponse returns the user to the index page
     */
    public function delete(Application $app, Request $request, $formname)
    {
        $data = $this->read();
        if (array_key_exists($formname, $data)) {
            unset($data[$formname]);
            $response = $this->write($data);
            if ($response) {
                $this->app['session']->getFlashBag()->set('success', Trans::__('Form successfully deleted'));
            }
        }

        return new RedirectResponse($this->app['url_generator']->generate('formeditor.index'));
    }

    /**
     * Internal method that initialises a new form with some sensible defaults.
     *
     * @param array $data data from the create form
     */
    protected function createForm($data)
    {
        $newname = $data['name'];

        $existing = $this->getForms($newname);

        if ($existing) {
            $this->app['session']->getFlashBag()->set('error', Trans::__('The form name you chose already exists'));

            return;
        }

        $cleanname = preg_replace('/[^a-zA-Z0-9_]/', '', $newname);

        $fulldata = $this->read();
        $fulldata[$cleanname] = $this->config['defaults'];

        if ($this->write($fulldata)) {
            $this->app['session']->getFlashBag()->set('success', Trans::__('Your new form has been created'));
        }
    }

    /**
     * Internal method that builds a Symfony Form from the name supplied.
     *
     * @param string $formname
     *
     * @return Form
     */
    protected function buildForm($formname)
    {
        $formdata = $this->getForms($formname);
        $formdata = $this->simplifyFormData($formdata);

        $form = $this->app['form.factory']
            ->createBuilder('form')
            ->add('notification', 'collection', ['type' => new Form\NotificationType(), 'data' => [$formdata['notification']]])
            ->add('feedback', 'collection', ['type' => new Form\FeedbackType(), 'data' => [$formdata['feedback']]])
            ->add('fields', 'collection',   [
                'type' => new Form\FormelementType(),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__name__',
                'data' => $formdata['fields'],
            ])
            ->getForm();

        return $form;
    }

    /**
     * Internal method that takes an array of posted data which
     * is in a slightly simplified structure and maps it correctly
     * to the Bolt Forms YML layout.
     *
     * @param array  $newdata  Posted data from the form
     * @param string $formname
     *
     * @return bool Whether the save was succesful
     */
    protected function save($newdata, $formname)
    {
        $fulldata = $this->read();

        if (isset($newdata['notification'])) {
            $fulldata[$formname]['notification'] = array_merge($fulldata[$formname]['notification'], $newdata['notification'][0]);
        }

        if (isset($newdata['feedback'])) {
            $fulldata[$formname]['feedback'] = array_merge($fulldata[$formname]['feedback'], $newdata['feedback'][0]);
        }

        if (isset($newdata['fields']['_delete'])) {
            foreach ($newdata['fields']['_delete'] as $field) {
                if (array_key_exists($field, $fulldata[$formname]['fields'])) {
                    unset($fulldata[$formname]['fields'][$field]);
                }
            }
            unset($newdata['fields']['_delete']);
        }

        if (isset($newdata['fields'])) {
            foreach ($newdata['fields'] as $name => $values) {
                if (is_numeric($name)) {
                    $fieldkey = preg_replace('/[^a-zA-Z0-9_]/', '', $values['name']);
                    $fulldata[$formname]['fields'][$fieldkey] = [];
                } else {
                    $fieldkey = $name;
                }

                $fulldata[$formname]['fields'][$fieldkey]['type'] = $values['type'];
                $fulldata[$formname]['fields'][$fieldkey]['options']['label'] = $values['label'];
                if ($values['required'] == true) {
                    $fulldata[$formname]['fields'][$fieldkey]['options']['required'] = true;
                } elseif ($values['type'] != 'submit') {
                    $fulldata[$formname]['fields'][$fieldkey]['options']['required'] = false;
                } else {
                    unset($fulldata[$formname]['fields'][$fieldkey]['options']['required']);
                }

                if ($values['type'] == 'choice') {
                    $fulldata[$formname]['fields'][$fieldkey]['options']['choices'] = $values['choices'];
                }
            }
        }

        // Final pass to sort the fields by order posted
        uksort($fulldata[$formname]['fields'], function ($a, $b) use ($newdata) {
            $apos = array_search($a, array_keys($newdata['fields']));
            $bpos = array_search($b, array_keys($newdata['fields']));
            if ($apos < $bpos) {
                return -1;
            }

            return 1;
        });

        return $this->write($fulldata);
    }

    /**
     * This method is to make the data structure easier to pass to Symfony
     * Forms for data binding. Reduces some of the more nested options into
     * a single level.
     *
     * @param array $data
     *
     * @return array The reorganised data array
     */
    protected function simplifyFormData($data)
    {
        foreach ($data['fields'] as $field => &$options) {
            $data['fields'][$field]['name'] = $field;
            $data['fields'][$field] = array_merge($data['fields'][$field], (array) $data['fields'][$field]['options']);

            if (isset($data['fields'][$field]['choices'])) {
                $data['fields'][$field]['data'] = $data['fields'][$field]['choices'];
            }
            unset($data['fields'][$field]['options']);
        }

        return $data;
    }

    /**
     * Handles reading the Bolt Forms yml file.
     *
     * @return array The parsed data
     */
    protected function read()
    {
        $file = $this->app['resources']->getPath('config/extensions/boltforms.bolt.yml');
        $yaml = file_get_contents($file);
        $parser = new Parser();
        $data = $parser->parse($yaml);

        return $data;
    }

    /**
     * Reads the raw data, unsets the non-form settings and returns.
     * If passed a parameter then only the single form data will be returned.
     *
     * @param string|null $form
     *
     * @return array
     */
    protected function getForms($form = false)
    {
        $data = $this->read();

        if (class_exists(BoltFormsExtension::class)) {
            $unsetKeys = $this->app['extensions']->get('Bolt/BoltForms')->getConfigKeys();
            foreach ($unsetKeys as $unsetKey) {
                unset($data[$unsetKey]);
            }
        }

        if ($form && array_key_exists($form, $data)) {
            return $data[$form];
        } elseif ($form) {
            return false;
        }

        return $data;
    }

    /**
     * Internal method that handles writing the data array back to the YML file.
     *
     * @param array $data
     *
     * @return bool True if successful
     */
    protected function write($data)
    {
        $dumper = new Dumper();
        $dumper->setIndentation(4);
        $yaml = $dumper->dump($data, 9999);
        $file = $this->app['resources']->getPath('config/extensions/boltforms.bolt.yml');
        try {
            $response = @file_put_contents($file, $yaml);
        } catch (\Exception $e) {
            $response = null;
        }

        return $response;
    }
}
