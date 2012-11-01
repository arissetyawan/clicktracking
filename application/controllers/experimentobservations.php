<?php 

class Experimentobservations_Controller extends Base_Controller
{
	public $restful = true;

	public function get_index()
	{
		$experimentobservations = Experimentobservation::all();
		$experimentobservationsArray = array();

		foreach ($experimentobservations as $experimentobservation)
		{
			$experimentobservationsArray[] = $experimentobservation->to_array();
		}

		return Response::json($experimentobservationsArray, 200, array('Access-Control-Allow-Origin' => Request::header('Origin', '*')));
	}

	public function get_experimentobservation($id)
	{
		$experimentobservation = Experimentobservation::find((int)$id);

		if ($experimentobservation) 
		{
			return Response::json($experimentobservation->to_array());
		} else {
			return Response::error(404);
		}
	}

	public function post_index()
	{
		$input = Input::json();
		$results = array();

		if (!is_array($input)) {
			$input = array($input);
		}

		foreach ($input as $observation)
		{
			$experimentobservation = DB::table('experimentobservations')
				->where('experiment_id', '=', $observation->experiment_id)
				->where('experiment_subject_id', '=', $observation->experiment_subject_id)
				->where('session', '=', $observation->session)
				->get();

			if ($experimentobservation) {
				$experimentobservation->clicks = (int)$experimentobservation->clicks + (int)$observation->clicks;
			} else {
				$experimentobservation = new Experimentobservation(array(
					'experiment_id' => $observation->experiment_id,
					'experiment_subject_id' => $observation->experiment_subject_id,
					'clicks' => $observation->clicks,
					'store_id' => $observation->store_id,
					'username' => $observation->username,
					'session' => $observation->session,
					'session_start' => $observation->session_start,
					'session_updated_at' => $observation->session_updated_at
				));
			}

			$experimentobservation->save();
			$results[] = $experimentobservation;
		}

		Response::json($results, 200, array(
			'Access-Control-Allow-Origin' => Request::header('Origin', '*'),
		));
	}
}