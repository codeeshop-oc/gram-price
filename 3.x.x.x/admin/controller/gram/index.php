<?php
class ControllerGramIndex extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('gram/index');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->request->post['module_gram_status'] = 1;

			$this->model_setting_setting->editSetting('module_gram', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$module_gram_price = (float)$this->request->post['module_gram_price'];

			$sql = "UPDATE " . DB_PREFIX . "product SET price = (" . $module_gram_price . " * weight)";

			$this->db->query($sql);
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}


		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('gram/index', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('gram/index', 'user_token=' . $this->session->data['user_token'], true);

		if (isset($this->request->post['module_gram_price'])) {
			$data['module_gram_price'] = $this->request->post['module_gram_price'];
		} else {
			$data['module_gram_price'] = $this->config->get('module_gram_price');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('gram/index', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'gram/index')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}