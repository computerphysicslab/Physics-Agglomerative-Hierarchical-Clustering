<?
	// Physics Agglomerative Hierarchical Clustering
	// Find clusters in data-mining through the power of physics methods
	// https://github.com/computerphysicslab/Physics-Agglomerative-Hierarchical-Clustering
	// http://en.wikipedia.org/wiki/Hierarchical_clustering
	// http://www.quora.com/Algorithms/How-does-Google-News-cluster-stories
	// Developed by Juan Ignacio Pérez Sacristán, 2014
	// http://www.linkedin.com/in/semanticwebarchitect
	// Semantic Web Architect - R&D
	
	function AHCenergy($clusters, $couplings) {
		$energy = 0;

		foreach($clusters as $centroid => $cluster) {
			foreach($cluster as $friend => $dummy) {
				if ($centroid == $friend) continue;
				$energy += $couplings[$centroid][$friend]/pow(count($cluster), 0.2);
			}
		}
		
		return $energy;
	}
	
	// $item absorbs $candidate shaping a new cluster configuration
	function AHCfussion($candidate, $item, $clusters) {
		foreach($clusters[$candidate] as $candidateFriends => $dummy) {
			$clusters[$item][$candidateFriends] = 1;
			unset($clusters[$candidate]);
		}
		
		return $clusters;
	}
	
	function AHCdiagonalize($couplings) {
		// Items are couplings indexes
		$items = array_keys($couplings);
		
		// Initial status: a cluster for every item
		foreach($items as $item) {
			$clusters[$item][$item] = 1;
		}
		
		// Compute initial Energy
		$E = AHCenergy($clusters, $couplings);
		
		// Dynamic Loop
		do {
			// Flag to tell if something really changed
			$didCommit = 0;
			
			// Memory for renders
			$bestE = 0;
			$bestCandidate = '';
			$bestItem = '';
			
			// Propose a change for every centroid
			foreach($items as $item) {
				// Quit if it is not a centroid
				if (!isset($clusters[$item])) continue;
				
				// Search other centroids (candidates) to fussion with this centroid
				foreach($clusters as $candidate => $cluster) {
					if ($candidate == $item) continue;
					
					// Imagine a new cluster configuration called $render
					$render = AHCfussion($candidate, $item, $clusters);
					
					// Compute new Energy
					$renderE = AHCenergy($render, $couplings);
					
					// Memorize if this is the best render yet
					if ($renderE > $bestE) {
						$bestE = $renderE;
						$bestCandidate = $candidate;
						$bestItem = $item;
					}
				}
			}
			
			// If better energy, commit the best fussion
			if ($bestE > $E) {
				// Render again the best fussion
				$clusters = AHCfussion($bestCandidate, $bestItem, $clusters);
				$E = $bestE;
				$didCommit = 1;
			}
			
		} while($didCommit);
		
		return $clusters;
	}
	
	// Order clusters setting best centrroid and friends by coupling decreasing
	function AHCorderByCoupling($clusters, $couplings) {
		foreach($clusters as $dummy => $cluster) {
			$goodFeeling = array();

			// Computes integrated couplings for different centroids of the cluster
			foreach($cluster as $centroid => $dummy2) {
				$goodFeeling[$centroid] = 0;
				foreach($cluster as $friend => $dummy3) {
					if ($centroid == $friend) continue;
					$goodFeeling[$centroid] += $couplings[$centroid][$friend];
				}
			}
			arsort($goodFeeling);
			reset($goodFeeling); $bestCentroid = key($goodFeeling);

			// Once found best centroid, assign a new cluster to it
			foreach($cluster as $friend => $dummy3) {
				if ($bestCentroid == $friend) continue;
				$newCluster[$bestCentroid][$friend] = $couplings[$bestCentroid][$friend];
				arsort($newCluster[$bestCentroid]);
			}
		}
		
		return $newCluster;
	}
	
	function AHCtest() {
		$couplings = array(
			'John' => array(
				'Eve' => 10,
				'Alice' => 4,
				'Paula' => 4,
			),
			'Eve' => array(
				'John' => 10,
				'Alice' => 4,
				'Paula' => 4,
			),
			'Alice' => array(
				'Paula' => 10,
				'John' => 4,
				'Eve' => 4,
			),
			'Paula' => array(
				'Alice' => 10,
				'John' => 4,
				'Eve' => 4,
			),
		);

		echo "<pre>"; print_r($couplings); echo "</pre><hr>";
		$clusters = AHCdiagonalize($couplings);
		echo "\n<hr>clusters:<pre>" . print_r($clusters, 1) . "</pre>";
	}
	
	AHCtest();
?>
